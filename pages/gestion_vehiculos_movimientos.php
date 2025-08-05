<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

$filtro_apellido = $_GET['apellido'] ?? '';
$filtro_semana = $_GET['semana'] ?? '';
$param_apellido = '%' . strtolower($filtro_apellido) . '%';

$where = "WHERE LOWER(e.apellido_completo) LIKE ?";
$params = [$param_apellido];

if ($filtro_semana !== '') {
    $where .= " AND EXTRACT(WEEK FROM m.fecha_movimiento) = ?";
    $params[] = (int)$filtro_semana;
}

$sql = "
    SELECT m.id, v.placa, v.modelo, e.nombre_completo || ' ' || e.apellido_completo AS estudiante,
           m.fecha_movimiento, m.hora_movimiento
    FROM movimientos_vehiculos m
    JOIN vehiculos v ON m.vehiculo_id = v.id
    JOIN estudiantes e ON v.estudiante_id = e.id
    $where
    ORDER BY m.fecha_movimiento DESC, m.hora_movimiento DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta - Movimientos de Vehículos</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body { background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; color: white; }
        .contenedor {
            max-width: 90%; background-color: rgba(0,0,0,0.7);
            margin: 30px auto; padding: 30px; border-radius: 10px;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
        }
        h2 { text-align: center; margin-bottom: 25px; }
        form { text-align: center; margin-bottom: 30px; }
        form input, form select {
            padding: 10px; margin: 0 10px; border-radius: 5px; border: none;
        }
        .btn { padding: 10px 20px; background-color: #2980b9; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        table {
            width: 100%; border-collapse: collapse; background-color: white;
            color: #333; border-radius: 8px; overflow: hidden;
        }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #2c3e50; color: white; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="contenedor">
    <h2>📋 Consulta de Entrada/Salida de Vehículos</h2>

    <form method="GET" action="">
        <input type="text" name="apellido" placeholder="Apellido del Estudiante" value="<?= htmlspecialchars($filtro_apellido) ?>">
        <input type="number" name="semana" min="1" max="53" placeholder="Semana (1-53)" value="<?= htmlspecialchars($filtro_semana) ?>">
        <button type="submit" class="btn">🔍 Buscar</button>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">🔙 Menú</a>
        <a href="/ceia_swga/src/reports_generators/pdf_movimientos_vehiculos.php?apellido=<?= urlencode($filtro_apellido) ?>&semana=<?= urlencode($filtro_semana) ?>" target="_blank" class="btn">📄 Exportar PDF</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Placa</th>
                <th>Modelo</th>
                <th>Estudiante</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($movimientos): ?>
            <?php foreach ($movimientos as $i => $m): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($m['placa']) ?></td>
                    <td><?= htmlspecialchars($m['modelo']) ?></td>
                    <td><?= htmlspecialchars($m['estudiante']) ?></td>
                    <td><?= $m['fecha_movimiento'] ?></td>
                    <td><?= $m['hora_movimiento'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay movimientos registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>