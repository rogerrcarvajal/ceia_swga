<?php
session_start();
// --- Bloque de seguridad ---
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php");
    exit();
}
// NOTA: No restringimos por rol aquí para permitir acceso a 'consulta'.
// La restricción se aplica en la barra de navegación (navbar).
require_once __DIR__ . '/../src/config.php';

// --- Obtener datos para los filtros ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Obtener la lista de grados que tienen estudiantes asignados en este período
$sql_grados = "SELECT DISTINCT grado_cursado FROM estudiante_periodo WHERE periodo_id = :pid ORDER BY grado_cursado";
$stmt_grados = $conn->prepare($sql_grados);
$stmt_grados->execute([':pid' => $periodo_activo['id']]);
$grados_con_estudiantes = $stmt_grados->fetchAll(PDO::FETCH_COLUMN, 0);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Control de Late-Pass</title>
    <link rel="stylesheet" href="/public/css/estilo_roster.css"> <style>
        .filtros-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .filtros-container select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA" style="width:150px;">
        <h1>Gestión de Control de Late-Pass</h1>
        <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
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
                    <th style="width: 100px; text-align:center;">Strikes Semanales</th>
                </tr>
            </thead>
            <tbody id="tabla_resultados_latepass">
                <tr><td colspan="5" style="text-align:center;">Seleccione una semana y un grado para ver los registros.</td></tr>
            </tbody>
        </table>
        <br><br>
        <!-- Botón para salir -->
        <a href="/pages/menu_latepass.php" class="btn">Salir</a>
    </div>
    <script src="/public/js/gestion_latepass.js"></script>
</body>
</html>