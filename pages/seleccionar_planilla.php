<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
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

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder inscribir estudiantes.";
}
$estudiantes = [];

if ($periodo_activo) {
    $periodo_id = $periodo_activo['id'];
    
    // ESTA ES LA CONSULTA CORREGIDA
    // Selecciona los estudiantes que tienen una entrada en 'estudiante_periodo' para el período activo.
    $sql = "SELECT e.id, e.nombre_completo, e.apellido_completo
            FROM estudiante_periodo ep
            JOIN estudiantes e ON ep.estudiante_id = e.id
            WHERE ep.periodo_id = :pid
            ORDER BY e.apellido_completo, e.nombre_completo";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pid' => $periodo_id]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Planilla de Inscripción - Generar PDF</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_admin.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 250px; margin-bottom: 30px;}
        .content h1 { font-size: 50px; margin-bottom: 20px;}
        .content p { font-size: 20px;}
        .right-panel { width: 30%; flex: 1; background-color: rgba(0,0,0,0.3); backdrop-filter:blur(5px); padding: 15px; border-radius: 8px; }

    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Generar Planilla de Inscripción</h1></div>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96; text-align: center;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    <div class="right-panel">
        <form action="/ceia_swga/src/reports_generators/generar_planilla_pdf.php" method="GET" target="_blank">
            <label>Seleccione un Estudiante del Período Actual:</label>
            <select name="id" required>
                <option value="">-- Por favor, elija --</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit">Generar PDF</button>
            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/menu_reportes.php" class="btn">Volver</a> 

        </form>
    </div>
</body>
</html>