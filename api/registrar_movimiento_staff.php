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
    // Verificar existencia
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

    $fecha = date('Y-m-d');
    $hora_actual = date('H:i:s');

    // Buscar si ya tiene registro hoy
    $check = $conn->prepare("SELECT * FROM entrada_salida_staff WHERE profesor_id = ? AND fecha = ?");
    $check->execute([$id, $fecha]);
    $registro = $check->fetch(PDO::FETCH_ASSOC);

    if (!$registro) {
        // Primera entrada del día
        $conn->prepare("
            INSERT INTO entrada_salida_staff (profesor_id, fecha, hora_entrada, ausente)
            VALUES (?, ?, ?, false)
        ")->execute([$id, $fecha, $hora_actual]);
    } else {
        // Ya tiene entrada, registrar salida si no existe
        if (!$registro['hora_salida']) {
            $update = $conn->prepare("
                UPDATE entrada_salida_staff SET hora_salida = ? WHERE id = ?
            ");
            $update->execute([$hora_actual, $registro['id']]);
        }
    }

    echo json_encode([
        'status' => 'exito',
        'nombre_completo' => $profesor['nombre_completo'],
        'posicion' => $profesor['posicion'] ?? 'No asignada',
        'hora' => $hora_actual,
        'mensaje' => 'Movimiento registrado.'
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}