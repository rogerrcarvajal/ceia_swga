<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    $id_del_padre_a_buscar = $_GET['id'] ?? null;
    if (!$id_del_padre_a_buscar) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }
    
    $stmt = $conn->prepare("SELECT * FROM padres WHERE padre_id = :id_padre");
    $stmt->execute([':id_padre' => $id_del_padre_a_buscar]);
    $padre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$padre) {
        echo json_encode(['error' => 'No se encontró información para el padre.']);
        exit;
    }
    echo json_encode($padre);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>