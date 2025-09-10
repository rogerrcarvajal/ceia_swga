<?php
error_reporting(0);
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo'])) {
    $response['message'] = 'Acceso denegado o código no proporcionado.';
    echo json_encode($response);
    exit();
}

try {
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'STF-') !== 0) {
        throw new Exception('El código QR no corresponde a un miembro del Staff.');
    }

    $staff_id = (int) substr($codigo, 4);

    $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    $fecha_actual = $dt->format('Y-m-d');
    $hora_actual = $dt->format('H:i:s');

    $sql_prof = "SELECT p.nombre_completo, p.categoria, pp.posicion 
                 FROM profesores p 
                 LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id AND pp.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1)
                 WHERE p.id = :id";
    $stmt_prof = $conn->prepare($sql_prof);
    $stmt_prof->execute([':id' => $staff_id]);
    $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);

    if (!$profesor) {
        throw new Exception("Miembro del staff no encontrado.");
    }

    $nombre_profesor = $profesor['nombre_completo'];
    $posicion = $profesor['posicion'] ?: $profesor['categoria'];

    $conn->beginTransaction();

    // --- LÓGICA DE REGISTRO EN movimientos_staff ---
    $stmt_last_mov = $conn->prepare("SELECT tipo_movimiento FROM movimientos_staff WHERE staff_id = :staff_id AND fecha_registro = :fecha ORDER BY hora_registro DESC LIMIT 1");
    $stmt_last_mov->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual]);
    $last_mov = $stmt_last_mov->fetch(PDO::FETCH_ASSOC);

    $tipo_movimiento = '';

    if (!$last_mov) {
        // PRIMER REGISTRO DEL DÍA: ENTRADA
        $tipo_movimiento = 'ENTRADA';
    } else if ($last_mov['tipo_movimiento'] == 'ENTRADA') {
        // SEGUNDO REGISTRO DEL DÍA: SALIDA
        $tipo_movimiento = 'SALIDA';
    } else {
        // YA REGISTRÓ ENTRADA Y SALIDA
        throw new Exception("Ya se ha completado el ciclo de entrada y salida para hoy.");
    }

    $sql_insert = "INSERT INTO movimientos_staff (staff_id, fecha_registro, hora_registro, tipo_movimiento) VALUES (:staff_id, :fecha, :hora, :tipo)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->execute([
        ':staff_id' => $staff_id,
        ':fecha' => $fecha_actual,
        ':hora' => $hora_actual,
        ':tipo' => $tipo_movimiento
    ]);

    $conn->commit();

    $response['success'] = true;
    $response['message'] = "✅ {$tipo_movimiento} registrada para {$nombre_profesor}.";
    $response['data'] = [
        'tipo' => 'STF',
        'nombre_completo' => $nombre_profesor,
        'posicion' => $posicion,
        'hora_registrada' => $hora_actual,
        'tipo_movimiento' => $tipo_movimiento
    ];

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);