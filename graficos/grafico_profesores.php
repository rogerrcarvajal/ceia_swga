<?php
session_start();
require_once "conn/conexion.php";

$sql = "SELECT p.nombre_completo, COUNT(*) as total
        FROM entrada_salida_profesores m
        JOIN profesores p ON m.profesor_id = p.id
        GROUP BY p.nombre_completo
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
    <title>Gráfico Profesores</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="login-box">
        <h2>Frecuencia de Entradas/Salidas - Profesores</h2>
        <canvas id="graficoProfesores" width="400" height="200"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('graficoProfesores').getContext('2d');
        const graficoProfesores = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($nombres) ?>,
                datasets: [{
                    label: 'Cantidad de Movimientos',
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