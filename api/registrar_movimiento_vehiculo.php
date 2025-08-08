<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$input = json_decode(file_get_contents('php://input'), true);
$qr_id = $input['qr_id'] ?? null;
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

    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    // Verificar si hay entrada registrada
    $check = $conn->prepare("SELECT * FROM registro_vehiculos WHERE vehiculo_id = ? AND fecha = ?");
    $check->execute([$id, $fecha]);
    $registro = $check->fetch(PDO::FETCH_ASSOC);

    if (!$registro) {
        $conn->prepare("
            INSERT INTO registro_vehiculos (vehiculo_id, fecha, hora_entrada, registrado_por, creado_en)
            VALUES (?, ?, ?, 'QR', NOW())
        ")->execute([$id, $fecha, $hora]);
    } elseif (!$registro['hora_salida']) {
        $conn->prepare("
            UPDATE registro_vehiculos SET hora_salida = ?, registrado_por = 'QR' WHERE id = ?
        ")->execute([$hora, $registro['id']]);
    }

    echo json_encode([
        'status' => 'exito',
        'registros' => [[
            'descripcion' => $vehiculo['placa'] . ' - ' . $vehiculo['modelo'] . ' (Familia ' . $vehiculo['apellido_completo'] . ')',
            'fecha' => $fecha,
            'hora_entrada' => $hora,
            'hora_salida' => $registro['hora_salida'] ?? null,
            'registrado_por' => 'QR',
            'observaciones' => $registro['observaciones'] ?? '',
            'mensaje' => 'Movimiento de vehículo registrado.'
        ]]
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}