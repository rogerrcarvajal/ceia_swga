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

// --- OBTENER LISTA DE TODOS LOS REPRESENTANTES (PADRES Y MADRES) ---
$sql = "(SELECT id, padre_nombre as nombre, padre_apellido as apellido, 'padre' as tipo FROM padres)
        UNION
        (SELECT id, madre_nombre as nombre, madre_apellido as apellido, 'madre' as tipo FROM madres)
        ORDER BY apellido, nombre";

$representantes = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Expedientes de Representantes</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css">
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content"><h1>Administrar Expedientes de Representantes</h1></div>

    <div class="main-container">
        <div class="left-panel">
            <h3>Lista de Representantes</h3>
            <input type="text" id="filtro_representantes" placeholder="Buscar por apellido...">
            <ul id="lista_representantes">
                <?php foreach ($representantes as $r): ?>
                    <li data-id="<?= $r['id'] ?>" data-tipo="<?= $r['tipo'] ?>">
                        <?= htmlspecialchars($r['apellido'] . ', ' . $r['nombre']) ?> (<?= ucfirst($r['tipo']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="right-panel">
            <div id="panel_informativo"><p>Seleccione un representante de la lista.</p></div>
            
            <div id="panel_datos_representantes" style="display:none;">
                <div id="mensaje_actualizacion" class="mensaje" style="display:none;"></div>
                <div class="form-grid">
                    <form id="form_padre" style="display:none;">
                        <h3>Datos del Padre</h3>
                        <input type="hidden" name="id" id="padre_id">
                        <input type="text" name="padre_nombre" id="padre_nombre" placeholder="Nombres completos" required>
                        <input type="text" name="padre_apellido" id="apellido_completo" placeholder="Apellidos completos" required>
                        <input type="date" name="padre_fecha_nacimiento" id="padre_fecha_nacimiento" required>
                        <input type="text" name="padre_cedula_pasaporte" id="padre_cedula_pasaporte" placeholder="Cédula o Pasaporte" required>
                        <input type="text" name="padre_nacionalidad" id="padre_nacionalidad" placeholder="Nacionalidad" required>
                        <input type="text" name="idioma" id="idioma" placeholder="Idiomas que habla" required>
                        <input type="text" name="padre_profesion" id="padre_profesion" placeholder="Profesión" required>
                        <input type="text" name="padre_empresa" id="padre_empresa" placeholder="Empresa donde trabaja" required>
                        <input type="text" name="padre_telefono_trabajo" id="padre_telefono_trabajo" placeholder="Teléfono de Trabajo">
                        <input type="text" name="padre_celular" id="padre_celular" placeholder="Teléfono celular">
                        <input type="text" name="padre_email" id="padre_email" placeholder="Correo electr[onico" required>

                        <button type="submit">Actualizar Padre</button>
                    </form>

                    <form id="form_madre" style="display:none;">
                        <h3>Datos de la Madre</h3>
                        <input type="hidden" name="id" id="madre_id">
                        <input type="text" name="madre_nombre" id="madre_nombre" placeholder="Nombres completos" required>
                        <input type="text" name="madre_apellido" id="madre_apellido" placeholder="Apellidos completos" required>
                        <input type="date" name="madre_fecha_nacimiento" id="madre_fecha_nacimiento" required>
                        <input type="text" name="madre_cedula_pasaporte" id="madre_cedula_pasaporte" placeholder="Cédula o Pasaporte" required>
                        <input type="text" name="madre_nacionalidad" id="madre_nacionalidad" placeholder="Nacionalidad" required>
                        <input type="text" name="idioma" id="idioma" placeholder="Idiomas que habla" required>
                        <input type="text" name="madre_profesion" id="madre_profesion" placeholder="Profesión" required>
                        <input type="text" name="madre_empresa" id="madre_empresa" placeholder="Empresa donde trabaja" required>
                        <input type="text" name="madre_telefono_trabajo" id="madre_telefono_trabajo" placeholder="Teléfono de Trabajo">
                        <input type="text" name="madre_celular" id="madre_celular" placeholder="Teléfono celular">
                        <input type="text" name="madre_email" id="madre_email" placeholder="Correo electronico" required>

                        <button type="submit">Actualizar Madre</button>
                    </form>

                    <div class="related-section">
                        <h3>Estudiantes Vinculados</h3>
                        <ul id="estudiantes_vinculados_lista"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/public/js/admin_representantes.js"></script>
</body>
</html>