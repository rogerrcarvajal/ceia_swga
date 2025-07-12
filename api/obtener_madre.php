<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    $id_del_madre_a_buscar = $_GET['id'] ?? null;
    if (!$id_del_madre_a_buscar) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }
    
    $stmt = $conn->prepare("SELECT * FROM madres WHERE madre_id = :id_madre");
    $stmt->execute([':id_madre' => $id_del_madre_a_buscar]);
    $madre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$madre) {
        echo json_encode(['error' => 'No se encontró información para el padre.']);
        exit;
    }
    echo json_encode($madre);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>