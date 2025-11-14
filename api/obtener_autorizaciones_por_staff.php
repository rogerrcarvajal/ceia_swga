<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$staff_id = filter_input(INPUT_GET, 'staff_id', FILTER_VALIDATE_INT);

if (!$staff_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de staff no válido o no proporcionado.']);
    exit();
}

try {
    $stmt = $conn->prepare(
        "SELECT id, fecha_permiso, hora_salida, duracion_horas, motivo
         FROM autorizaciones_salida_staff
         WHERE profesor_id = :staff_id
         ORDER BY fecha_permiso DESC, hora_salida DESC"
    );
    $stmt->execute([':staff_id' => $staff_id]);
    $autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($autorizaciones);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener las autorizaciones: ' . $e->getMessage()]);
}
?>