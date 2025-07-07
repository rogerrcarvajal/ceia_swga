<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");

$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
$periodo_stmt = $conn->query(query: "SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() === 0) {
    // Si no hay período activo, se guarda un mensaje de error en la sesión.
    // La ventana modal se encargará de mostrarlo.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar o crear uno en el menú de Mantenimiento para poder continuar.";
}

// Verificar si se ha enviado el ID del estudiante a editar
$estudiante_id = $_GET['id'] ?? null;   

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

        // 1. Insertar PADRE y obtener su ID
        $check = $conn->prepare(query: "SELECT id FROM padres WHERE padre_cedula_pasaporte = :padre_cedula_pasaporte");
        $check->execute(params: [':padre_cedula_pasaporte' => $padre_cedula_pasaporte = $_POST['padre_cedula_pasaporte']]);

         // Verificar si ya existe un padre con la misma cédula o pasaporte 

        if ($check->rowCount() > 0) {
            $mensaje = "⚠️ Ya existe un padre registrado con esa cédula/pasaporte.";
        } else {
            $sql_padre = "INSERT INTO padres (estudiante_id, padre_nombre, padre_apellido, padre_fecha_nacimiento, padre_cedula_pasaporte, padre_nacionalidad, padre_idioma, padre_profesion, padre_empresa, padre_telefono_trabajo, padre_celular, padre_email)
                VALUES (:estudiante_id, :padre_nombre, :padre_apellido, :padre_fecha_nacimiento, :padre_cedula_pasaporte, :padre_nacionalidad, :padre_idioma, :padre_profesion, :padre_empresa, :padre_telefono_trabajo, :padre_celular, :padre_email)";
            $stmt_padre = $conn->prepare($sql_padre);
            $stmt_padre->execute([
                ':estudiante_id' => $estudiante_id,
                ':padre_nombre' => $_POST['padre_nombre'], 
                ':padre_apellido' => $_POST['padre_apellido'], 
                ':padre_fecha_nacimiento' => $_POST['padre_fecha_nacimiento'],
                ':padre_cedula_pasaporte' => $_POST['padre_cedula_pasaporte'], 
                ':padre_nacionalidad' => $_POST['padre_nacionalidad'], 
                ':padre_idioma' => $_POST['padre_idioma'],
                ':padre_profesion' => $_POST['padre_profesion'], 
                ':padre_empresa' => $_POST['padre_empresa'], 
                ':padre_telefono_trabajo' => $_POST['padre_telefono_trabajo'],
                ':padre_celular' => $_POST['padre_celular'], 
                ':padre_email' => $_POST['padre_email']
            ]);
            $padre_id = $conn->lastInsertId();
        }

        // 2. Insertar MADRE y obtener su ID
        $check = $conn->prepare("SELECT id FROM madres WHERE madre_cedula_pasaporte = :madre_cedula_pasaporte");
        $check->execute([':madre_cedula_pasaporte' => $madre_cedula_pasaporte = $_POST['madre_cedula_pasaporte']]);

         // Verificar si ya existe una madre con la misma cédula o pasaporte    

        if ($check->rowCount() > 0) {
            $mensaje = "⚠️ Ya existe una madre registrada con esa cédula/pasaporte.";
        } else {
            $sql_madre = "INSERT INTO madres (estudiante_id, madre_nombre, madre_apellido, madre_fecha_nacimiento, madre_cedula_pasaporte, madre_nacionalidad, madre_idioma, madre_profesion, madre_empresa, madre_telefono_trabajo, madre_celular, madre_email) 
                VALUES (:estudiante_id, :madre_nombre, :madre_apellido, :madre_fecha_nacimiento, :madre_cedula_pasaporte, :madre_nacionalidad, :madre_idioma, :madre_profesion, :madre_empresa, :madre_telefono_trabajo, :madre_celular, :madre_email)";
            $stmt_madre = $conn->prepare($sql_madre);
            $stmt_madre->execute([
                ':estudiante_id' => $estudiante_id,
                ':madre_nombre' => $_POST['madre_nombre'], 
                ':madre_apellido' => $_POST['madre_apellido'], 
                ':madre_fecha_nacimiento' => $_POST['madre_fecha_nacimiento'],
                ':madre_cedula_pasaporte' => $_POST['madre_cedula_pasaporte'], 
                ':madre_nacionalidad' => $_POST['madre_nacionalidad'], 
                ':madre_idioma' => $_POST['madre_idioma'],
                ':madre_profesion' => $_POST['madre_profesion'], 
                ':madre_empresa' => $_POST['madre_empresa'], 
                ':madre_telefono_trabajo' => $_POST['madre_telefono_trabajo'],
                ':madre_celular' => $_POST['madre_celular'], 
                ':madre_email' => $_POST['madre_email']
            ]);
            $madre_id = $conn->lastInsertId();
        }
        
        // 3. Insertar ESTUDIANTE con los IDs de padre y madre
        $sql_estudiante = "INSERT INTO estudiantes (nombre_completo, apellido_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, grado_ingreso, fecha_inscripcion, recomendado_por, edad_estudiante, staff, activo, padre_id, madre_id) 
                           VALUES (:nombre_completo, :apellido_completo, :fecha_nacimiento, :lugar_nacimiento, :nacionalidad, :idioma, :direccion, :telefono_casa, :telefono_movil, :telefono_emergencia, :grado_ingreso, :fecha_inscripcion, :recomendado_por, :edad_estudiante, :staff, :activo, :padre_id, :madre_id)";
        $stmt_estudiante = $conn->prepare($sql_estudiante);
        $staff = isset($_POST['staff']) ? 1 : 0;
        $activo = isset($_POST['activo']) ? 1 : 0;
        $stmt_estudiante->execute([
             ':nombre_completo' => $_POST['nombre_completo'], 
             ':apellido_completo' => $_POST['apellido_completo'], 
             ':fecha_nacimiento' => $_POST['fecha_nacimiento'],
             ':lugar_nacimiento' => $_POST['lugar_nacimiento'], 
             ':nacionalidad' => $_POST['nacionalidad'], 
             ':idioma' => $_POST['idioma'],
             ':direccion' => $_POST['direccion'], 
             ':telefono_casa' => $_POST['telefono_casa'], 
             ':telefono_movil' => $_POST['telefono_movil'],
             ':telefono_emergencia' => $_POST['telefono_emergencia'], 
             ':grado_ingreso' => $_POST['grado_ingreso'], 
             ':fecha_inscripcion' => $_POST['fecha_inscripcion'],
             ':recomendado_por' => $_POST['recomendado_por'], 
             ':edad_estudiante' => $_POST['edad_estudiante'], 
             ':staff' => $staff,
             ':activo' => $activo,
             ':padre_id' => $padre_id, 
             ':madre_id' => $madre_id
        ]);
        $estudiante_id = $conn->lastInsertId();

        // 4. Insertar FICHA MÉDICA con el ID del estudiante
        $sql_ficha = "INSERT INTO salud_estudiantil (estudiante_id, completado_por, fecha_salud, contacto_emergencia, relacion_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional, problemas_oido_vista, fecha_examen, autorizo_medicamentos, medicamentos_actuales, autorizo_emergencia)
                      VALUES (:estudiante_id, :completado_por, :fecha_salud, :contacto_emergencia, :relacion_emergencia, :telefono1, :telefono2, :observaciones, :dislexia, :atencion, :otros, :info_adicional, :problemas_oido_vista, :fecha_examen, :autorizo_medicamentos, :medicamentos_actuales, :autorizo_emergencia)";
        $stmt_ficha = $conn->prepare($sql_ficha);
        $stmt_ficha->execute([
            ':estudiante_id' => $estudiante_id, 
            ':completado_por' => $_POST['completado_por'], 
            ':fecha_salud' => $_POST['fecha_salud'],
            ':contacto_emergencia' => $_POST['contacto_emergencia'], 
            ':relacion_emergencia' => $_POST['relacion_emergencia'], 
            ':telefono1' => $_POST['telefono1'], 
            ':telefono2' => $_POST['telefono2'],
            ':observaciones' => $_POST['observaciones'], 
            ':dislexia' => isset($_POST['dislexia']) ? 1 : 0, 
            ':atencion' => isset($_POST['atencion']) ? 1 : 0, 
            ':otros' => isset($_POST['otros']) ? 1 : 0,
            ':info_adicional' => $_POST['info_adicional'], 
            ':problemas_oido_vista' => $_POST['problemas_oido_vista'], 
            ':fecha_examen' => $_POST['fecha_examen'],
            ':autorizo_medicamentos' => isset($_POST['autorizo_medicamentos']) ? 1 : 0, 
            ':medicamentos_actuales' => $_POST['medicamentos_actuales'], 
            ':autorizo_emergencia' => isset($_POST['autorizo_emergencia']) ? 1 : 0
        ]);

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
    <title>Planilla de Inscripción - CEIA</title>
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
         body {
            margin: 0;
            padding: 0;
            background-image: url("/public/img/fondo.jpg");
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        
        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 85%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        .form-seccion {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
        }

        .form-seccionFM {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
            justify-content: left;
        }

        .h1 {
            color: white;
            text-align: center;
            margin-bottom: 0px;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        .container { background-color: rgba(0, 0, 0, 0.8); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 85%; box-shadow: 0 4px 8px rgba(0,0,0,0.5); }
        
        .content {
            text-align: center;
            margin-top: 30px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .formulario h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .alerta {
            background: #ddffdd;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid green;
        }

        .alerta-error {
            background: #ffdddd;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid red;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Planilla de Inscripción</h1>

    <div class="container"> 
    <form method="POST">
        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>"><?= $mensaje ?></p>
        <?php endif; ?>
    <div class="contenedor-principal">
        <div class="panel-derecho">
            <div id="mensaje_actualizacion" style="color: lightgreen; margin-bottom: 15px;"></div>
            <h3>Datos del Estudiante</h3>
            <form id="form_estudiante">
                <input type="text" name="nombre_completo" placeholder="Nombres completo" required>
                <input type="text" name="apellido_completo" placeholder="Apellidos completo" required>
                <input type="date" name="fecha_nacimiento" required>
                <input type="text" name="lugar_nacimiento" placeholder="Lugar de nacimiento" required>
                <input type="text" name="nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="idioma" placeholder="Idiomas que habla" required>
                <textarea name="direccion" placeholder="Dirección" required></textarea>
                <input type="text" name="telefono_casa" placeholder="Teléfono de casa" required>
                <input type="text" name="telefono_movil" placeholder="Teléfono celular" required>
                <input type="text" name="telefono_emergencia" placeholder="Teléfono de emergencia" required>
                <select name="grado_ingreso" required>
                    <option value="">Grado de ingreso</option>
                    <option value="Daycare">Daycare</option>
                    <option value="Pk-3">Pk-3</option>
                    <option value="Pk-4">Pk-4</option>
                    <option value="Kindergarten">Kindergarten</option>
                    <option value="Grade 1">Grade 1</option>
                    <option value="Grade 2">Grade 2</option>
                    <option value="Grade 3">Grade 3</option>
                    <option value="Grade 4">Grade 4</option>
                    <option value="Grade 5">Grade 5</option>
                    <option value="Grade 6">Grade 6</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                    <option value="Grade 11">Grade 11</option>
                    <option value="Grade 12">Grade 12</option>
                </select>
                <input type="date" name="fecha_inscripcion" required>
                <input type="text" name="recomendado_por" placeholder="Recomendado por">
                <input type="number" name="edad_estudiante" placeholder="Edad" required>
                <label><input type="checkbox" name="staff"> Estudiante Staff</label><br><br>
                <label><input type="checkbox" name="activo"> Estudiante Activo</label><br><br>
            </form>
        </div>

        <div class="panel-derecho">
            <h3>Datos del Padre</h3>
            <form id="form_padre">
                <input type="text" name="padre_nombre" placeholder="Nombre" required>
                <input type="text" name="padre_apellido" placeholder="Apellido" required>
                <input type="date" name="padre_fecha_nacimiento" required>
                <input type="text" name="padre_cedula_pasaporte" placeholder="Cédula o Pasaporte" required>
                <input type="text" name="padre_nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="padre_idioma" placeholder="Idiomas que habla" required>
                <input type="text" name="padre_profesion" placeholder="Profesión" required>
                <input type="text" name="padre_empresa" placeholder="Empresa donde trabaja" required>
                <input type="text" name="padre_telefono_trabajo" placeholder="Teléfono trabajo" required>
                <input type="text" name="padre_celular" placeholder="Celular" required>
                <input type="email" name="padre_email" placeholder="Correo electrónico" required>
            </form>
        </div>
            
        <div class="panel-derecho">
            <h3>Datos de la Madre</h3>
            <form id="form_madre">
                <input type="text" name="madre_nombre" placeholder="Nombre" required>
                <input type="text" name="madre_apellido" placeholder="Apellido" required>
                <input type="date" name="madre_fecha_nacimiento" required>
                <input type="text" name="madre_cedula_pasaporte" placeholder="Cédula o Pasaporte" required>
                <input type="text" name="madre_nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="madre_idioma" placeholder="Idiomas que habla" required>
                <input type="text" name="madre_profesion" placeholder="Profesión" required>
                <input type="text" name="madre_empresa" placeholder="Empresa donde trabaja" required>
                <input type="text" name="madre_telefono_trabajo" placeholder="Teléfono trabajo" required>
                <input type="text" name="madre_celular" placeholder="Celular" required>
                <input type="email" name="madre_email" placeholder="Correo electrónico" required>
            </form>
        </div>

        <div class="panel-derecho">
            <h3>Ficha Médica</h3>
            <form id="form_ficha_medica">
                <input type="text" name="completado_por" placeholder="Completado por" required>
                <input type="date" name="fecha_salud" required>
                <input type="text" name="contacto_emergencia" placeholder="Contacto de Emergencia" required>
                <input type="text" name="relacion_emergencia" placeholder="Relación de Emergencia" required>
                <input type="text" name="Teléfono1" placeholder="Teléfono 1" required>
                <input type="text" name="Teléfono1" placeholder="Teléfono 2">
                <textarea name="observaciones" placeholder="Observaciones"></textarea>

                <label><input type="checkbox" name="dislexia"> Dislexia</label>
                <label><input type="checkbox" name="atencion"> Déficit de Atención</label>
                <label><input type="checkbox" name="otros"> Otros</label>

                <textarea name="info_adicional" placeholder="Información adicional"></textarea>
                <textarea name="problemas_oido_vista" placeholder="Problemas de oído/vista"></textarea>
                <input type="text" name="fecha_examen" placeholder="Fecha último examen oído/vista">

                <label><input type="checkbox" name="autorizo_medicamentos"> Autorizo administración de medicamentos</label>
                <textarea name="medicamentos_actuales" placeholder="Medicamentos actuales"></textarea>

                <label><input type="checkbox" name="autorizo_emergencia"> Autorizo atención de emergencia</label>

                <br><br>
            </form>
            <button type="submit">Guardar Inscripción</button>
        </div>
    </div>
    </form>
    </div> 
</body>
</html>
