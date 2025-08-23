<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$semana = $_GET['semana'] ?? '';
$vehiculo_id = $_GET['vehiculo_id'] ?? '';

if (!$semana || !$vehiculo_id || $vehiculo_id === 'todos') {
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

$sql = "SELECT rv.fecha, rv.hora_entrada, rv.hora_salida, rv.registrado_por, rv.observaciones,
        v.placa || ' - ' || v.modelo || ' (Familia ' || e.apellido_completo || ')' AS descripcion
        FROM registro_vehiculos rv
        JOIN vehiculos v ON rv.vehiculo_id = v.id
        JOIN estudiantes e ON v.estudiante_id = e.id
        WHERE rv.vehiculo_id = :vehiculo_id AND rv.fecha BETWEEN :inicio AND :fin
        ORDER BY rv.fecha ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([':vehiculo_id' => $vehiculo_id, ':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'exito', 'registros' => $registros]);
