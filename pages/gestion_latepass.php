<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// --- BLOQUE DE CONTROL DE ACCESO CORREGIDO ---
// Permite el acceso si el rol es 'admin' O 'consulta'.
if ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'master' && $_SESSION['usuario']['rol'] !== 'consulta') {
    $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// --- Obtener datos para los filtros ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$grados_con_estudiantes = [];
if ($periodo_activo) {
    $sql_grados = "SELECT DISTINCT grado_cursado FROM estudiante_periodo WHERE periodo_id = :pid ORDER BY grado_cursado";
    $stmt_grados = $conn->prepare($sql_grados);
    $stmt_grados->execute([':pid' => $periodo_activo['id']]);
    $grados_con_estudiantes = $stmt_grados->fetchAll(PDO::FETCH_COLUMN, 0);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestión y consulta de Late-Pass</title>
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
        <h1>Gestión y consulta de Late-Pass</h1>
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
                <label for="filtro_grado">Seleccionar Grado:</label>
                <select id="filtro_grado">
                    <option value="todos">Todos los Grados</option>
                    <?php foreach ($grados_con_estudiantes as $grado): ?>
                        <option value="<?= htmlspecialchars($grado) ?>"><?= htmlspecialchars($grado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="staff-table">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Grado</th>
                    <th>Fecha de Llegada</th>
                    <th>Hora de Llegada</th>
                    <th style="text-align:center">Strikes Semanales</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados_latepass">
                <tr><td colspan="6" style="text-align:center;">Seleccione una semana y un grado para ver los registros.</td></tr>
            </tbody>
        </table>
        <br><br>
        <button id="btnGenerarPDF" class="btn">📄 Generar PDF</button>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
    </div>
    <script src="/ceia_swga/public/js/gestion_latepass.js"></script>
</body>
</html>
