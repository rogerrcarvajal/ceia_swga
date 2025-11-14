<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    // Se espera recibir el ID de la madre directamente.
    $madre_id = $_GET['id'] ?? null;
    if (!$madre_id) {
        throw new InvalidArgumentException('ID de madre no proporcionado.');
    }

    // Obtener los datos de la madre usando el ID proporcionado.
    $stmt_madre = $conn->prepare("SELECT * FROM madres WHERE madre_id = :id_madre");
    $stmt_madre->execute([':id_madre' => $madre_id]);
    $madre = $stmt_madre->fetch(PDO::FETCH_ASSOC);

    if (!$madre) {
        // Si no se encuentra, devolver null para que el frontend lo maneje.
        echo json_encode(null);
        exit;
    }

    echo json_encode($madre);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>