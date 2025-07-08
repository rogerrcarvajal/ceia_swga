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
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// Obtener lista de estudiantes
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Expedientes</title>
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("/public/img/fondo.jpg");
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
        }
        
        h3 {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        select {
        width: 100%;
        height: 500px;
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
            margin-bottom: 0px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Administrar Planilla de Inscripción</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="contenedor-principal">
        <div class="panel-izquierdo">
            <h3>Lista de Estudiantes</h3>
            <select id="lista_estudiantes" size= "20">
                <option value="">-- Seleccione un estudiante --</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="panel-derecho">            
            <h3>Datos del Estudiante</h3>
            <div id="mensaje_actualizacion" style="color: lightgreen; margin-bottom: 15px;"></div>
            <form id="form_estudiante">
                <input type="hidden" name="id" id="estudiante_id">
                <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Nombres completo" required>
                <input type="text" name="apellido_completo" id="apellido_completo" placeholder="Apellidos completo" required>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                <input type="text" name="lugar_nacimiento" id="lugar_nacimiento" placeholder="Lugar de nacimiento" required>
                <input type="text" name="nacionalidad" id="nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="idioma" id="idioma" placeholder="Idiomas que habla" required>
                <textarea name="direccion" id="direccion" placeholder="Dirección" required></textarea>
                <input type="text" name="telefono_casa" id="telefono_casa" placeholder="Teléfono de casa">
                <input type="text" name="telefono_movil" id="telefono_movil" placeholder="Teléfono celular">
                <input type="text" name="telefono_emergencia" id="telefono_emergencia" placeholder="Teléfono de emergencia" required>
                <input type="text" name="grado_ingreso" id="grado_ingreso" placeholder="Grado de ingreso" required>
                <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" required>
                <input type="text" name="recomendado_por" id="recomendado_por" placeholder="Recomendado por">
                <input type="number" name="edad_estudiante" id="edad_estudiante" placeholder="Edad" required>

                <label><input type="checkbox" name="staff" id="staff"> Estudiante Staff</label><br><br>
                <label><input type="checkbox" name="activo" id="activo"> Estudiante Activo</label><br><br>

                <button type="submit">Actualizar Estudiante</button>
            </form>
        </div>

        <div class="panel-derecho">
            <h3>Datos del Padre</h3>
            <form id="form_padre">
                <input type="hidden" name="estudiante_id" id="estudiante_id_padre">                
                <input type="text" name="padre_nombre" id="padre_nombre" placeholder="Nombre del Padre" >
                <input type="text" name="padre_apellido" id="padre_apellido" placeholder="Apellido del Padre" >
                <input type="date" name="padre_fecha_nacimiento" id="padre_fecha_nacimiento" >
                <input type="text" name="padre_cedula_pasaporte" id="padre_cedula_pasaporte" placeholder="Cédula o Pasaporte" >
                <input type="text" name="padre_nacionalidad" id="padre_nacionalidad" placeholder="Nacionalidad" >
                <input type="text" name="padre_idioma" id="padre_idioma" placeholder="Idiomas que habla" >
                <input type="text" name="padre_profesion" id="padre_profesion" placeholder="Profesión" >
                <input type="text" name="padre_empresa" id="padre_empresa" placeholder="Empresa donde trabaja" >
                <input type="text" name="padre_telefono_trabajo" id="padre_telefono_trabajo" placeholder="Teléfono trabajo" >
                <input type="text" name="padre_celular" id="padre_celular" placeholder="Celular" >
                <input type="email" name="padre_email" id="padre_email" placeholder="Correo electrónico" ><br><br>
                <button type="button" id="actualizar_padre">Actualizar Padre</button>
            </form>
        </div>

        <div class="panel-derecho">
            <h3>Datos de la Madre</h3>
            <form id="form_madre">   
                <input type="hidden" name="estudiante_id" id="estudiante_id_madre">
                <input type="text" name="madre_nombre" id="madre_nombre" placeholder="Nombre de la Madre" >
                <input type="text" name="madre_apellido" id="madre_apellido" placeholder="Apellido de la Madre" >
                <input type="date" name="madre_fecha_nacimiento" id="madre_fecha_nacimiento" >
                <input type="text" name="madre_cedula_pasaporte" id="madre_cedula_pasaporte" placeholder="Cédula o Pasaporte" >
                <input type="text" name="madre_nacionalidad" id="madre_nacionalidad" placeholder="Nacionalidad" >
                <input type="text" name="madre_idioma" id="madre_idioma" placeholder="Idiomas que habla" >
                <input type="text" name="madre_profesion" id="madre_profesion" placeholder="Profesión" >
                <input type="text" name="madre_empresa" id="madre_empresa" placeholder="Empresa donde trabaja" >
                <input type="text" name="madre_telefono_trabajo" id="madre_telefono_trabajo" placeholder="Teléfono trabajo" >
                <input type="text" name="madre_celular" id="madre_celular" placeholder="Celular" >
                <input type="email" name="madre_email" id="madre_email" placeholder="Correo electrónico" ><br><br>
                <button type="button" id="actualizar_madre">Actualizar Madre</button>
            </form>
        </div>

        <div class="panel-derecho">
            <h3>Ficha Médica</h3>
            <form id="form_ficha_medica">
                <input type="hidden" name="estudiante_id" id="estudiante_id_medica">
                <input type="text" name="completado_por" id="completado_por" placeholder="Completado por" >
                <input type="date" name="fecha_salud" id="fecha_salud" >
                <input type="text" name="contacto_emergencia" id="contacto_emergencia" placeholder="Contacto de Emergencia" >
                <input type="text" name="relacion_emergencia" id="relacion_emergencia" placeholder="Relación de Emergencia" >
                <input type="text" name="telefono1" id="telefono1" placeholder="Teléfono 1" >
                <input type="text" name="telefono2" id="telefono2" placeholder="Teléfono 2">
                <textarea name="observaciones" id="observaciones" placeholder="Observaciones"></textarea>
                <label><input type="checkbox" name="dislexia" id="dislexia"> Dislexia</label>
                <label><input type="checkbox" name="atencion" id="atencion"> Déficit de Atención</label>
                <label><input type="checkbox" name="otros" id="otros"> Otros</label>
                <textarea name="info_adicional" id="info_adicional" placeholder="Información adicional"></textarea>
                <textarea name="problemas_oido_vista" id="problemas_oido_vista" placeholder="Problemas de oído/vista"></textarea>
                <input type="text" name="fecha_examen" id="fecha_examen" placeholder="Fecha último examen oído/vista">
                <label><input type="checkbox" name="autorizo_medicamentos" id="autorizo_medicamentos"> Autorizo administración de medicamentos</label>
                <textarea name="medicamentos_actuales" id="medicamentos_actuales" placeholder="Medicamentos actuales"></textarea>
                <label><input type="checkbox" name="autorizo_emergencia" id="autorizo_emergencia"> Autorizo atención de emergencia</label><br><br>
                <button type="button" id="actualizar_ficha_medica">Actualizar Ficha Médica</button>
            </form>
        </div>
    </div>
    
    <script src="/public/js/admin_expedientes.js"></script>

</body>
</html>
