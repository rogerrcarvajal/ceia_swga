<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    $estudiante_id = $_GET['estudiante_id'] ?? null;
    if (!$estudiante_id) {
        throw new InvalidArgumentException('ID de estudiante no proporcionado.');
    }

    // 1. Obtener el madre_id desde la tabla estudiantes
    $stmt_est = $conn->prepare("SELECT madre_id FROM estudiantes WHERE id = :estudiante_id");
    $stmt_est->execute([':estudiante_id' => $estudiante_id]);
    $result = $stmt_est->fetch(PDO::FETCH_ASSOC);

    if (!$result || empty($result['madre_id'])) {
        echo json_encode(null); // No hay madre asociada o no se encontró el estudiante
        exit;
    }

    $madre_id = $result['madre_id'];

    // 2. Obtener los datos de la madre usando el madre_id
    $stmt_madre = $conn->prepare("SELECT * FROM madres WHERE madre_id = :id_madre");
    $stmt_madre->execute([':id_madre' => $madre_id]);
    $madre = $stmt_madre->fetch(PDO::FETCH_ASSOC);

    if (!$madre) {
        echo json_encode(null); // El madre_id existe en estudiantes pero no en la tabla madres (integridad de datos)
        exit;
    }

    echo json_encode($madre);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>