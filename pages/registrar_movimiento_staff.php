<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$input = json_decode(file_get_contents('php://input'), true);
$staff_id = filter_var($input['staff_id'] ?? 0, FILTER_VALIDATE_INT);

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if (!$staff_id) {
    echo json_encode($response);
    exit();
}

try {
    $periodo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$periodo) throw new Exception("No hay período activo.");

    $hora_actual = date('H:i:s');
    $fecha_actual = date('Y-m-d');

    // Consultar último movimiento
    $stmt_ultimo = $conn->prepare("SELECT id, hora_salida FROM movimientos_staff WHERE staff_id = ? AND fecha = ? ORDER BY id DESC LIMIT 1");
    $stmt_ultimo->execute([$staff_id, $fecha_actual]);
    $ultimo = $stmt_ultimo->fetch(PDO::FETCH_ASSOC);

    if (!$ultimo || $ultimo['hora_salida']) {
        // Registrar nueva entrada
        $stmt_insert = $conn->prepare("INSERT INTO movimientos_staff (staff_id, fecha, hora_entrada) VALUES (?, ?, ?)");
        $stmt_insert->execute([$staff_id, $fecha_actual, $hora_actual]);
        $accion = "Entrada registrada";
    } else {
        // Registrar salida
        $stmt_update = $conn->prepare("UPDATE movimientos_staff SET hora_salida = ? WHERE id = ?");
        $stmt_update->execute([$hora_actual, $ultimo['id']]);
        $accion = "Salida registrada";
    }

    // Obtener datos del staff
    $stmt_info = $conn->prepare("SELECT p.nombre_completo, pp.posicion FROM profesores p JOIN profesor_periodo pp ON pp.profesor_id = p.id WHERE p.id = ? AND pp.periodo_id = ?");
    $stmt_info->execute([$staff_id, $periodo['id']]);
    $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

    $response = [
        'status' => 'exito',
        'nombre_completo' => $info['nombre_completo'],
        'posicion' => $info['posicion'],
        'hora' => $hora_actual,
        'mensaje' => $accion
    ];

} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit();