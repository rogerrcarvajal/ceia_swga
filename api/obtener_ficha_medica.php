<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    $id_de_la_ficha_a_buscar = $_GET['estudiante_id'] ?? null;
    if (!$id_de_la_ficha_a_buscar) {
        throw new InvalidArgumentException('ID de ficha Medica no proporcionado.');
    }
    
    $stmt = $conn->prepare("SELECT * FROM salud_estudiantil WHERE estudiante_id = :id_ficha");
    $stmt->execute([':id_ficha' => $id_de_la_ficha_a_buscar]);
    $ficha = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ficha) {
        echo json_encode(['error' => 'No se encontró información de la ficha Medica.']);
        exit;
    }
    echo json_encode($ficha);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>