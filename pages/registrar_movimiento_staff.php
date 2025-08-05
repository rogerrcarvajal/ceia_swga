<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$id = filter_var($qr_id ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido de staff.']);
    exit();
}

try {
    $stmt_prof = $conn->prepare("
        SELECT p.id, p.nombre_completo, pp.posicion
        FROM profesores p
        LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id
        WHERE p.id = :id
        ORDER BY pp.id DESC
        LIMIT 1
    ");
    $stmt_prof->execute([':id' => $id]);
    $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);

    if (!$profesor) {
        throw new Exception("Profesor no encontrado.");
    }

    $hora = date('H:i:s');
    $fecha = date('Y-m-d');

    $conn->prepare("
        INSERT INTO movimientos_staff (profesor_id, hora_movimiento, fecha_movimiento)
        VALUES (?, ?, ?)
    ")->execute([$id, $hora, $fecha]);

    echo json_encode([
        'status' => 'exito',
        'nombre_completo' => $profesor['nombre_completo'],
        'posicion' => $profesor['posicion'] ?? 'No asignada',
        'hora_llegada' => $hora,
        'mensaje' => 'Ingreso registrado exitosamente.'
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}