<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// Obtener el período escolar activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder inscribir estudiantes.";
}

// --- 2. LÓGICA DE PROCESAMIENTO DEL FORMULARIO (REDISEÑADA Y CORREGIDA) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_inscripcion'])) {
    try {
        $conn->beginTransaction();
        $periodo_id_activo = $periodo_activo['id'];

        // --- GESTIÓN INTELIGENTE DEL PADRE (A PRUEBA DE ERRORES) ---
        $padre_id = $_POST['padre_id_existente'] ?? null;
        if (empty($padre_id) && !empty($_POST['padre_cedula_pasaporte'])) {
            // ANTES DE INSERTAR, VERIFICAMOS SI LA CÉDULA YA EXISTE EN LA BD
            $stmt_check_padre = $conn->prepare("SELECT padre_id FROM padres WHERE padre_cedula_pasaporte = :cedula");
            $stmt_check_padre->execute([':cedula' => $_POST['padre_cedula_pasaporte']]);
            $padre_existente = $stmt_check_padre->fetch();

            if ($padre_existente) {
                // Si ya existe, usamos su ID
                $padre_id = $padre_existente['padre_id'];
            } else {
                // Si NO existe, procedemos a INSERTAR
                $sql_padre = "INSERT INTO padres (padre_nombre, padre_apellido, padre_fecha_nacimiento, padre_cedula_pasaporte, padre_nacionalidad, padre_idioma, padre_profesion, padre_empresa, padre_telefono_trabajo, padre_celular, padre_email)
                              VALUES (:nombre, :apellido, :nac, :ced, :nacd, :idioma, :prof, :emp, :tel_t, :cel, :email)";
                $stmt_padre = $conn->prepare($sql_padre);
                $stmt_padre->execute([
                    ':nombre' => $_POST['padre_nombre'], ':apellido' => $_POST['padre_apellido'],
                    ':nac' => $_POST['padre_fecha_nacimiento'], ':ced' => $_POST['padre_cedula_pasaporte'],
                    ':nacd' => $_POST['padre_nacionalidad'], ':idioma' => $_POST['padre_idioma'],
                    ':prof' => $_POST['padre_profesion'], ':emp' => $_POST['padre_empresa'],
                    ':tel_t' => $_POST['padre_telefono_trabajo'], ':cel' => $_POST['padre_celular'],
                    ':email' => $_POST['padre_email']
                ]);
                $padre_id = $conn->lastInsertId();
            }
        }

        // --- GESTIÓN INTELIGENTE DE LA MADRE (A PRUEBA DE ERRORES) ---
        $madre_id = $_POST['madre_id_existente'] ?? null;
        if (empty($madre_id) && !empty($_POST['madre_cedula_pasaporte'])) {
            // ANTES DE INSERTAR, VERIFICAMOS SI LA CÉDULA YA EXISTE EN LA BD
            $stmt_check_madre = $conn->prepare("SELECT madre_id FROM madres WHERE madre_cedula_pasaporte = :cedula");
            $stmt_check_madre->execute([':cedula' => $_POST['madre_cedula_pasaporte']]);
            $madre_existente = $stmt_check_madre->fetch();
            
            if ($madre_existente) {
                // Si ya existe, usamos su ID
                $madre_id = $madre_existente['madre_id'];
            } else {
                // Si NO existe, procedemos a INSERTAR
                $sql_madre = "INSERT INTO madres (madre_nombre, madre_apellido, madre_fecha_nacimiento, madre_cedula_pasaporte, madre_nacionalidad, madre_idioma, madre_profesion, madre_empresa, madre_telefono_trabajo, madre_celular, madre_email) 
                              VALUES (:nombre, :apellido, :nac, :ced, :nacd, :idioma, :prof, :emp, :tel_t, :cel, :email)";
                $stmt_madre = $conn->prepare($sql_madre);
                $stmt_madre->execute([
                    ':nombre' => $_POST['madre_nombre'], ':apellido' => $_POST['madre_apellido'],
                    ':nac' => $_POST['madre_fecha_nacimiento'], ':ced' => $_POST['madre_cedula_pasaporte'],
                    ':nacd' => $_POST['madre_nacionalidad'], ':idioma' => $_POST['madre_idioma'],
                    ':prof' => $_POST['madre_profesion'], ':emp' => $_POST['madre_empresa'],
                    ':tel_t' => $_POST['madre_telefono_trabajo'], ':cel' => $_POST['madre_celular'],
                    ':email' => $_POST['madre_email']
                ]);
                $madre_id = $conn->lastInsertId();
            }
        }
        
        // --- INSERCIÓN DEL ESTUDIANTE (CON LOS IDs CORRECTOS) ---
        $sql_estudiante = "INSERT INTO estudiantes (periodo_id, padre_id, madre_id, nombre_completo, apellido_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, fecha_inscripcion, recomendado_por, edad_estudiante, staff, estudiante_hermanos, colegios_anteriores) 
                           VALUES (:periodo_id, :padre_id, :madre_id, :nombre, :apellido, :fec_nac, :lug_nac, :nac, :idioma, :dir, :tel_c, :tel_m, :tel_e, :fec_ins, :rec, :edad, :staff, :hermanos, :colegios)";
        $stmt_estudiante = $conn->prepare($sql_estudiante);
        $stmt_estudiante->execute([
            ':periodo_id' => $periodo_id_activo, ':padre_id' => $padre_id, ':madre_id' => $madre_id,
            ':nombre' => $_POST['nombre_completo'], ':apellido' => $_POST['apellido_completo'], ':fec_nac' => $_POST['fecha_nacimiento'],
            ':lug_nac' => $_POST['lugar_nacimiento'], ':nac' => $_POST['nacionalidad'], ':idioma' => $_POST['idioma'],
            ':dir' => $_POST['direccion'], ':tel_c' => $_POST['telefono_casa'], ':tel_m' => $_POST['telefono_movil'],
            ':tel_e' => $_POST['telefono_emergencia'], ':fec_ins' => $_POST['fecha_inscripcion'],
            ':rec' => $_POST['recomendado_por'], ':edad' => $_POST['edad_estudiante'], 
            ':staff' => isset($_POST['staff']) ? 1 : 0,
            ':hermanos' => $_POST['estudiante_hermanos'], ':colegios' => $_POST['colegios_anteriores']
        ]);
        $estudiante_id = $conn->lastInsertId();

        // --- INSERCIÓN DE LA FICHA MÉDICA ---
        $sql_ficha = "INSERT INTO salud_estudiantil (estudiante_id, completado_por, fecha_salud, contacto_emergencia, relacion_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional, problemas_oido_vista, fecha_examen, autorizo_medicamentos, medicamentos_actuales, autorizo_emergencia)
                      VALUES (:est_id, :comp, :fec_sal, :cont, :rel, :tel1, :tel2, :obs, :dis, :aten, :otros, :info, :problemas_oido_vista, :fec_ex, :auto_med, :meds, :auto_em)";
        $stmt_ficha = $conn->prepare($sql_ficha);
        $stmt_ficha->execute([
            ':est_id' => $estudiante_id, ':comp' => $_POST['completado_por'], ':fec_sal' => $_POST['fecha_salud'],
            ':cont' => $_POST['contacto_emergencia'], ':rel' => $_POST['relacion_emergencia'], 
            ':tel1' => $_POST['telefono1'], ':tel2' => $_POST['telefono2'], ':obs' => $_POST['observaciones'], 
            ':dis' => isset($_POST['dislexia']) ? 1 : 0, ':aten' => isset($_POST['atencion']) ? 1 : 0, 
            ':otros' => isset($_POST['otros']) ? 1 : 0, ':info' => $_POST['info_adicional'], 
            ':problemas_oido_vista' => $_POST['problemas_oido_vista'], ':fec_ex' => $_POST['fecha_examen'],
            ':auto_med' => isset($_POST['autorizo_medicamentos']) ? 1 : 0, ':meds' => $_POST['medicamentos_actuales'],
            ':auto_em' => isset($_POST['autorizo_emergencia']) ? 1 : 0
        ]);

        // --- INSERCIÓN EN estudiante_periodo SI LA CASILLA ACTIVO ESTÁ MARCADA ---
        if (isset($_POST['activo'])) {
            $sql_asignacion = "INSERT INTO estudiante_periodo (estudiante_id, periodo_id, grado_cursado) VALUES (:eid, :pid, :grado)";
            $stmt_asig = $conn->prepare($sql_asignacion);
            $stmt_asig->execute([
                ':eid' => $estudiante_id,
                ':pid' => $periodo_id_activo,
                ':grado' => $_POST['grado_cursado'] ?? ''
            ]);
        }

        $conn->commit();
        $mensaje = "✅ Registro completado correctamente.";
    } catch (Exception $e) {
        $conn->rollBack();
        $mensaje = "❌ Error en el registro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Planilla de Inscripción</title>
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .container { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 95%; box-shadow: 0 4px 8px rgba(0,0,0,0.3); }
        .formulario-contenedor { background-color: rgba(255, 255, 255, 0.1); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 95%; display: flex; flex-wrap: wrap; justify-content: space-around; }
        .form-seccion { width: 10%; color: white; min-width: 280px;}
        .h1 { color: white; text-align: center; margin-bottom: 0px; }
        .h3 { text-align: center; margin-bottom: 20px; padding-bottom: 5px; color: white; }
        .content { text-align: center; margin-top: 30px; color: white; text-shadow: 1px 1px 2px black; }
        .content img { width: 180px; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 15px; font-size: 16px; box-sizing: border-box;}
        .alerta { padding: 10px; margin: 10px 0; border-left: 5px solid green; background-color: #ddffdd; color: #333; }
        .alerta-error { padding: 10px; margin: 10px 0; border-left: 5px solid red; background-color: #ffdddd; color: #333; }
        .resultado-busqueda { margin-top: -5px; margin-bottom: 15px; padding: 10px; border-radius: 5px; font-size: 0.9em; transition: all 0.3s; }
        .resultado-busqueda.encontrado { background-color: #e8f5e9; border: 1px solid #4caf50; color: #1b5e20; }
        .resultado-busqueda.no-encontrado { background-color: #fffde7; border: 1px solid #fbc02d; color: #f57f17; }
        .resultado-busqueda.vinculado { background-color: #4caf50; border: 1px solid #2e7d32; color: white; text-align: center; font-weight: bold;}
        .resultado-busqueda button { padding: 5px 10px; margin: 5px; cursor: pointer; border: 1px solid #ccc; background-color: #f0f0f0; }
        .btn { background-color: rgb(48, 48, 48); color: white; padding: 10px 18px; margin-top: 20px; text-decoration: none; display: inline-block; border-radius: 5px; cursor: pointer; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            async function buscarRepresentante(tipo) {
                const cedulaInput = document.getElementById(`${tipo}_cedula_pasaporte`);
                const resultadoDiv = document.getElementById(`resultado_${tipo}`);
                const cedula = cedulaInput.value.trim();

                if (cedula.length < 4) {
                    resultadoDiv.innerHTML = '';
                    ignorarBusqueda(tipo);
                    return;
                }

                try {
                    const url = `/ceia_swga/api/buscar_representante.php?tipo=${tipo}&cedula=${cedula}`;
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Error de red');
                    const data = await response.json();

                    if (document.getElementById(`${tipo}_id_existente`).value) return; // Si ya está vinculado, no hacer nada

                    if (data.encontrado) {
                        resultadoDiv.className = 'resultado-busqueda encontrado';
                        resultadoDiv.innerHTML = `<strong>¡Representante Encontrado!</strong><br>${data.nombre}<br>
                            <button type="button" onclick="vincularRepresentante('${tipo}', ${data.id}, '${data.nombre}')">Vincular</button>
                            <button type="button" onclick="ignorarBusqueda('${tipo}')">Ignorar y Registrar Nuevo</button>`;
                    } else {
                        resultadoDiv.className = 'resultado-busqueda no-encontrado';
                        resultadoDiv.innerHTML = "Cédula no encontrada. Se creará un nuevo registro.";
                    }
                } catch (error) { 
                    console.error('Error al buscar:', error);
                    resultadoDiv.className = 'resultado-busqueda no-encontrado';
                    resultadoDiv.innerHTML = "Error al buscar. Revise la consola para más detalles.";
                }
            }

            window.vincularRepresentante = function(tipo, id, nombre) {
                document.getElementById(`${tipo}_id_existente`).value = id;
                const resultadoDiv = document.getElementById(`resultado_${tipo}`);
                resultadoDiv.className = 'resultado-busqueda vinculado';
                resultadoDiv.innerHTML = `Vinculado a: ${nombre}`;
                
                const formDiv = document.getElementById(`form_${tipo}`);
                const inputs = formDiv.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.type !== 'hidden' && input.id !== `${tipo}_cedula_pasaporte`) {
                        input.disabled = true;
                        input.required = false;
                    }
                });
            }
            
            window.ignorarBusqueda = function(tipo) {
                document.getElementById(`${tipo}_id_existente`).value = '';
                const resultadoDiv = document.getElementById(`resultado_${tipo}`);
                if (!resultadoDiv.classList.contains('vinculado')) resultadoDiv.innerHTML = '';
                
                const formDiv = document.getElementById(`form_${tipo}`);
                const inputs = formDiv.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                    if(input.dataset.required === 'true') input.required = true;
                });
            }

                        document.getElementById('padre_cedula_pasaporte').addEventListener('blur', () => buscarRepresentante('padre'));
            document.getElementById('madre_cedula_pasaporte').addEventListener('blur', () => buscarRepresentante('madre'));
        });
    </script>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Planilla de Inscripción</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="container">
        <form method="POST">
            <?php if ($mensaje): ?>
                <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>"><?= $mensaje ?></p>
            <?php endif; ?>
            <div class="formulario-contenedor">
                <div class="form-seccion">
                    <h3>Datos del Estudiante</h3>
                    <input type="text" name="nombre_completo" placeholder="Nombres completo" required data-required="true">
                    <input type="text" name="apellido_completo" placeholder="Apellidos completo" required data-required="true">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" required data-required="true">
                    <input type="text" name="lugar_nacimiento" placeholder="Lugar de nacimiento" required data-required="true">
                    <input type="text" name="nacionalidad" placeholder="Nacionalidad" required data-required="true">
                    <input type="text" name="idioma" placeholder="Idiomas que habla" required data-required="true">
                    <textarea name="direccion" placeholder="Dirección" required data-required="true"></textarea>
                    <input type="text" name="telefono_casa" placeholder="Teléfono de casa">
                    <input type="text" name="telefono_movil" placeholder="Teléfono celular">
                    <input type="text" name="telefono_emergencia" placeholder="Teléfono de emergencia" required data-required="true">
                    <label for="fecha_inscripcion">Fecha de Inscripción:</label>
                    <input type="date" name="fecha_inscripcion" required data-required="true">
                    <input type="text" name="recomendado_por" placeholder="Recomendado por">
                    <input type="number" name="edad_estudiante" placeholder="Edad" required data-required="true">
                    <textarea name="estudiante_hermanos" placeholder="Hermanos estudiando en el CEIA"></textarea>
                    <input type="text" name="colegios_anteriores" placeholder="Colegio(s) donde estudió antes">
                    <label>Estudiante Staff<input type="checkbox" name="staff"></label><br><br>
<<<<<<< HEAD
=======
                    <label>Inscribir como Activo en este período<input type="checkbox" name="activo" checked></label><br><br>
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
                </div>

                <div class="form-seccion" id="form_padre">
                    <h3>Datos del Padre</h3>
                    <input type="hidden" name="padre_id_existente" id="padre_id_existente">
                    <input type="text" id="padre_cedula_pasaporte" name="padre_cedula_pasaporte" placeholder="Cédula o Pasaporte (Buscar...)" required data-required="true">
                    <div id="resultado_padre" class="resultado-busqueda"></div>
                    <input type="text" name="padre_nombre" placeholder="Nombre" required data-required="true">
                    <input type="text" name="padre_apellido" placeholder="Apellido" required data-required="true">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" name="padre_fecha_nacimiento" required data-required="true">
                    <input type="text" name="padre_nacionalidad" placeholder="Nacionalidad" required data-required="true">
                    <input type="text" name="padre_idioma" placeholder="Idiomas que habla" required data-required="true">
                    <input type="text" name="padre_profesion" placeholder="Profesión" required data-required="true">
                    <input type="text" name="padre_empresa" placeholder="Empresa donde trabaja" required data-required="true">
                    <input type="text" name="padre_telefono_trabajo" placeholder="Teléfono trabajo">
                    <input type="text" name="padre_celular" placeholder="Celular" required data-required="true">
                    <input type="email" name="padre_email" placeholder="Correo electrónico" required data-required="true">
                </div>
                
                <div class="form-seccion" id="form_madre">
                    <h3>Datos de la Madre</h3>
                    <input type="hidden" name="madre_id_existente" id="madre_id_existente">
                    <input type="text" id="madre_cedula_pasaporte" name="madre_cedula_pasaporte" placeholder="Cédula o Pasaporte (Buscar...)" required data-required="true">
                    <div id="resultado_madre" class="resultado-busqueda"></div>
                    <input type="text" name="madre_nombre" placeholder="Nombre" required data-required="true">
                    <input type="text" name="madre_apellido" placeholder="Apellido" required data-required="true">
                    <label for="madre_fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" name="madre_fecha_nacimiento" required data-required="true">
                    <input type="text" name="madre_nacionalidad" placeholder="Nacionalidad" required data-required="true">
                    <input type="text" name="madre_idioma" placeholder="Idiomas que habla" required data-required="true">
                    <input type="text" name="madre_profesion" placeholder="Profesión" required data-required="true">
                    <input type="text" name="madre_empresa" placeholder="Empresa donde trabaja" required data-required="true">
                    <input type="text" name="madre_telefono_trabajo" placeholder="Teléfono trabajo">
                    <input type="text" name="madre_celular" placeholder="Celular" required data-required="true">
                    <input type="email" name="madre_email" placeholder="Correo electrónico" required data-required="true">
                </div>

                <div class="form-seccion">
                    <h3>Ficha Médica</h3>
                    <input type="text" name="completado_por" placeholder="Completado por" required data-required="true">
                    <label for="fecha_salud">Fecha última actualización:</label>
                    <input type="date" name="fecha_salud" required data-required="true">
                    <input type="text" name="contacto_emergencia" placeholder="Contacto de Emergencia" required data-required="true">
                    <input type="text" name="relacion_emergencia" placeholder="Relación de Emergencia" required data-required="true">
                    <input type="text" name="telefono1" placeholder="Teléfono 1" required data-required="true">
                    <input type="text" name="telefono2" placeholder="Teléfono 2">
                    <textarea name="observaciones" placeholder="Observaciones"></textarea>
                    <label>Dislexia<input type="checkbox" name="dislexia"></label>
                    <label>Déficit de Atención<input type="checkbox" name="atencion"></label>
                    <label>Informacion adicional<input type="checkbox" name="otros"></label>
                    <textarea name="info_adicional" placeholder="hospitalización, operaciones o heridas importantes, alergias a medicamentos y vacunas,medicamentos, defectos, alimentación o otras enfermedades graves."></textarea>
                    <textarea name="problemas_oido_vista" placeholder="Problemas de oído/vista"></textarea>
                    <input type="text" name="fecha_examen" placeholder="Fecha último examen oído/vista">
                    <label>Autorizo la administración de medicamentos<input type="checkbox" name="autorizo_medicamentos"></label>
                    <textarea name="medicamentos_actuales" placeholder="Medicamentos actuales"></textarea>
                    <label>Autorizo atención de emergencia<input type="checkbox" name="autorizo_emergencia"></label>
                    <br><br>
                </div>
            </div>
            <button type="submit" name="guardar_inscripcion" style="display: inline-block; margin-top: 20px; text-decoration: none; padding: 10px 15px; background-color:rgb(48, 48, 48); color: white; border-radius: 5px;">Guardar Planilla de Inscripción</button>
            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/menu_estudiantes.php" class="btn">Volver</a> 
        </form>
    </div>
</body>
</html>