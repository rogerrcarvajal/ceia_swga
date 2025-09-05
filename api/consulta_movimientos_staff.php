<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$semana = $_GET['semana'] ?? '';
$staff_id = $_GET['staff_id'] ?? '';

if (!$semana || !$staff_id || $staff_id === 'todos') {
    echo json_encode(['status' => 'exito', 'registros' => []]);
    exit();
}

// Convertir semana ISO a rango de fechas
$anio = substr($semana, 0, 4);
$semana_num = substr($semana, 6, 2);
$dt = new DateTime();
$dt->setISODate($anio, $semana_num);
$fecha_inicio = $dt->format('Y-m-d');
$dt->modify('+6 days');
$fecha_fin = $dt->format('Y-m-d');

$sql = "SELECT es.nombre_completo AS nombre, es.fecha AS fecha, es.hora_entrada, es.hora_salida, es.ausente
        FROM entrada_salida_staff es
        WHERE es.profesor_id = :staff_id AND es.fecha BETWEEN :inicio AND :fin
        ORDER BY es.fecha ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([':staff_id' => $staff_id, ':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'exito', 'registros' => $registros]);
