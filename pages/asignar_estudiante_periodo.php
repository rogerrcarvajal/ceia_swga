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

// Obtener todos los períodos escolares para el selector
$periodos = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares ORDER BY nombre_periodo DESC")->fetchAll(PDO::FETCH_ASSOC);

// Lista de grados disponibles para el formulario
$grados_disponibles = [
    'Daycare', 'Preschool', 'Prekinder 3', 'Prekinder 4', 'Kindergarten', 
    'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 
    'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Estudiantes a Período Escolar</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css"> </head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Asignar Estudiantes a Período</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="main-container">
        <div class="left-panel">
            <h3>Seleccionar Período</h3>
            <select id="periodo_selector">
                <option value="">-- Elija un período --</option>
                <?php foreach ($periodos as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_periodo']) ?></option>
                <?php endforeach; ?>
            </select>
            <hr>
            <h3>Estudiantes Asignados</h3>
            <ul id="lista_estudiantes_asignados"></ul>
        </div>

        <div class="right-panel">
            <div id="panel_informativo"><p>Seleccione un período para ver y asignar estudiantes.</p></div>
            <div id="panel_asignacion" style="display:none;">
                <div id="mensaje_asignacion" class="mensaje" style="display:none;"></div>
                <h3>Asignar Estudiante a este Período</h3>
                <form id="form_asignar_estudiante">
                    <input type="hidden" id="periodo_id_hidden" name="periodo_id">
                    
                    <label for="estudiante_id">Estudiante (No Asignado):</label>
                    <select name="estudiante_id" id="estudiante_id" required></select>

                    <label for="grado_cursado">Grado a cursar:</label>
                    <select name="grado_cursado" id="grado_cursado" required>
                        <?php foreach ($grados_disponibles as $grado): ?>
                            <option value="<?= $grado ?>"><?= htmlspecialchars($grado) ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit">Asignar Estudiante</button>
                    <a href="/pages/gestionar_estudiantes_2.php?id=<?= $p['id'] ?>">Gestionar Estudiantes</a>
                    <!-- Botón para volver al Home -->
                    <a href="/pages/menu_estudiantes.php" class="btn">Volver</a>
                </form>
            </div>
        </div>
    </div>
    <script src="/public/js/admin_asignar_estudiante.js"></script>
</body>
</html>