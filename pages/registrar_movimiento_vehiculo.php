<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$input = json_decode(file_get_contents('php://input'), true);
$vehiculo_id = filter_var($input['vehiculo_id'] ?? 0, FILTER_VALIDATE_INT);

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if (!$vehiculo_id) {
    echo json_encode($response);
    exit();
}

try {
    $hora_actual = date('H:i:s');
    $fecha_actual = date('Y-m-d');

    // Consultar último movimiento
    $stmt_ultimo = $conn->prepare("SELECT id, hora_salida FROM movimientos_vehiculos WHERE vehiculo_id = ? AND fecha = ? ORDER BY id DESC LIMIT 1");
    $stmt_ultimo->execute([$vehiculo_id, $fecha_actual]);
    $ultimo = $stmt_ultimo->fetch(PDO::FETCH_ASSOC);

    if (!$ultimo || $ultimo['hora_salida']) {
        // Registrar entrada
        $stmt_insert = $conn->prepare("INSERT INTO movimientos_vehiculos (vehiculo_id, fecha, hora_entrada) VALUES (?, ?, ?)");
        $stmt_insert->execute([$vehiculo_id, $fecha_actual, $hora_actual]);
        $accion = "Entrada registrada";
    } else {
        // Registrar salida
        $stmt_update = $conn->prepare("UPDATE movimientos_vehiculos SET hora_salida = ? WHERE id = ?");
        $stmt_update->execute([$hora_actual, $ultimo['id']]);
        $accion = "Salida registrada";
    }

    // Obtener datos del vehículo y familia asociada
    $stmt_info = $conn->prepare("SELECT v.placa, v.descripcion, f.apellido_familia FROM vehiculos v JOIN estudiantes e ON e.id = v.estudiante_id JOIN familias f ON f.id = e.familia_id WHERE v.id = ?");
    $stmt_info->execute([$vehiculo_id]);
    $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

    $response = [
        'status' => 'exito',
        'familia' => $info['apellido_familia'],
        'placa' => $info['placa'],
        'descripcion' => $info['descripcion'],
        'hora' => $hora_actual,
        'mensaje' => $accion
    ];

} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit();