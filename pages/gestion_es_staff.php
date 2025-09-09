<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo el Usuario Master y Admin pueden gestionar el módulo Staff.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo el Usuario Master y Admin pueden gestionar el módulo Staff."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// --- Obtener datos para los filtros ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$staff_members = [];
if ($periodo_activo) {
    $sql_staff = "SELECT id, nombre_completo, apellido_completo FROM profesores ORDER BY nombre_completo, apellido_completo";
    $stmt_staff = $conn->prepare($sql_staff);
    $stmt_staff->execute();
    $staff_members = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestión Asistencia de Staff</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_roster.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .filtros-container { display: flex; gap: 20px; margin-bottom: 20px; padding: 15px; background-color: rgba(0,0,0,0.1); border-radius: 8px; }
        .filtros-container select, .filtros-container input { padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Gestión y consulta de Entrada/Salida<br>Staff/Profesores</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="filtros-container">
            <div>
                <label for="filtro_semana">Seleccionar Semana:</label>
                <input type="week" id="filtro_semana">
            </div>
            <div>
                <label for="filtro_staff">Seleccionar Personal:</label>
                <select id="filtro_staff">
                    <option value="todos">Todo el Personal</option>
                    <?php foreach ($staff_members as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre_completo'] . ' ' . $s['apellido_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Nombre Staff/Profesor</th>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Ausente</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados_staff">
                <tr><td colspan="6" style="text-align:center;">Seleccione una semana y un Staff para ver los registros.</td></tr>
            </tbody>
        </table>
        <br>
        <button id="btnGenerarPDF" class="btn">📄 Generar PDF</button>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
    </div>
    <script src="/ceia_swga/public/js/gestion_es_staff.js"></script>
</body>
</html>