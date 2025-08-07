<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$id = filter_var($qr_id ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido de vehículo.']);
    exit();
}

try {
    $stmt_veh = $conn->prepare("
        SELECT v.id, v.placa, v.modelo, e.apellido_completo
        FROM vehiculos v
        JOIN estudiantes e ON v.estudiante_id = e.id
        WHERE v.id = ?
    ");
    $stmt_veh->execute([$id]);
    $vehiculo = $stmt_veh->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo) {
        throw new Exception("Vehículo no registrado o no autorizado.");
    }

    $hora = date('H:i:s');
    $fecha = date('Y-m-d');

    $conn->prepare("
        INSERT INTO movimientos_vehiculos (vehiculo_id, hora_movimiento, fecha_movimiento)
        VALUES (?, ?, ?)
    ")->execute([$id, $hora, $fecha]);

    echo json_encode([
        'status' => 'exito',
        'placa' => $vehiculo['placa'],
        'modelo' => $vehiculo['modelo'],
        'apellido_familia' => $vehiculo['apellido_completo'],
        'hora_llegada' => $hora,
        'mensaje' => 'Movimiento registrado.'
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}