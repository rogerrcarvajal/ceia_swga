<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$estudiante_id = filter_input(INPUT_GET, 'estudiante_id', FILTER_VALIDATE_INT);

if (!$estudiante_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de estudiante no válido o no proporcionado.']);
    exit();
}

try {
    $stmt = $conn->prepare(
        "SELECT id, fecha_salida, hora_salida, retirado_por_nombre, motivo
         FROM autorizaciones_salida
         WHERE estudiante_id = :estudiante_id
         ORDER BY fecha_salida DESC, hora_salida DESC"
    );
    $stmt->execute([':estudiante_id' => $estudiante_id]);
    $autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($autorizaciones);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener las autorizaciones: ' . $e->getMessage()]);
}
?>