<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master', 'consulta'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$vehiculos = [];

if ($periodo_activo) {
    $stmt = $conn->query("
        SELECT v.id, v.placa || ' - ' || v.modelo || ' (' || e.nombre_completo || ' ' || e.apellido_completo || ')' AS descripcion
        FROM vehiculos v
        JOIN estudiantes e ON v.estudiante_id = e.id
        ORDER BY descripcion
    ");
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Gestión Vehicular QR</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/estilo_roster.css">
    <style>
        body { background-image: url("/ceia_swga/public/img/fondo.jpg"); color: white; }
        .filtros-container { display: flex; gap: 20px; margin-bottom: 20px; padding: 15px; background-color: rgba(0,0,0,0.1); border-radius: 8px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Gestión de Entrada/Salida de Vehículos</h1>
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
                <label for="filtro_vehiculo">Seleccionar Vehículo:</label>
                <select id="filtro_vehiculo">
                    <option value="todos">Todos</option>
                    <?php foreach ($vehiculos as $v): ?>
                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['descripcion']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Registrado Por</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados_vehiculos">
                <tr><td colspan="6" style="text-align:center;">Seleccione una semana y un vehículo para ver los registros.</td></tr>
            </tbody>
        </table>
        <br><br>
        <button id="btnGenerarPDF" class="btn">📄 Generar PDF</button>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
    </div>

    <script src="/ceia_swga/public/js/gestion_vehiculos.js"></script>
</body>
</html>