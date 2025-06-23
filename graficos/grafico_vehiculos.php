<?php
session_start();
require_once "conn/conexion.php";

$sql = "SELECT e.nombre_completo, COUNT(*) as total
        FROM vehiculos_autorizados v
        JOIN estudiantes e ON v.estudiante_id = e.id
        GROUP BY e.nombre_completo
        ORDER BY total DESC";

$stmt = $conn->query($sql);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombres = [];
$totales = [];

foreach ($datos as $row) {
    $nombres[] = $row['nombre_completo'];
    $totales[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gráfico Vehículos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="login-box">
        <h2>Frecuencia de Vehículos por Estudiante</h2>
        <canvas id="graficoVehiculos" width="400" height="200"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('graficoVehiculos').getContext('2d');
        const graficoVehiculos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($nombres) ?>,
                datasets: [{
                    label: 'Cantidad de Registros',
                    data: <?= json_encode($totales) ?>,
                    backgroundColor: 'rgba(0, 87, 160, 0.7)'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
</body>
</html>