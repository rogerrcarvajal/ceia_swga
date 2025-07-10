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


// --- 2. OBTENER LISTA DE PADRES Y MADRES PARA EL PANEL IZQUIERDO ---
// Unimos ambas tablas para tener una sola lista de representantes
$padres_sql = "(SELECT id, padre_nombre as nombre, padre_apellido as apellido, 'padre' as tipo FROM padres)
               UNION
               (SELECT id, madre_nombre as nombre, madre_apellido as apellido, 'madre' as tipo FROM madres)
               ORDER BY apellido, nombre";
$representantes = $conn->query($padres_sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Expedientes de Padres</title>
    <link rel="stylesheet" href="/css/estilo_admin.css"> </head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <h1>Administrar Expedientes de Padres y Representantes</h1>
    </div>

    <div class="main-container">
        <div class="left-panel">
            <h3>Lista de Representantes</h3>
            <input type="text" id="filtro_representantes" placeholder="Buscar representante...">
            <ul id="lista_representantes">
                <?php foreach ($representantes as $r): ?>
                    <li data-id="<?= $r['id'] ?>" data-tipo="<?= $r['tipo'] ?>">
                        <?= htmlspecialchars($r['apellido'] . ', ' . $r['nombre']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="right-panel">
            <div id="panel_informativo">
                <p>Seleccione un representante de la lista para ver su información.</p>
            </div>
            
            <div id="panel_datos_representante" style="display:none;">
                <div id="mensaje_actualizacion_padre" class="mensaje"></div>

                <form id="form_representante">
                    <h3>Datos del Representante</h3>
                    <input type="hidden" name="id" id="representante_id">
                    <input type="hidden" name="tipo" id="representante_tipo">
                    <button type="submit">Actualizar Representante</button>
                </form>

                <div class="related-section">
                    <h3>Estudiantes Vinculados</h3>
                    <ul id="lista_estudiantes_vinculados">
                        </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/js/admin_padres.js"></script>
</body>
</html>