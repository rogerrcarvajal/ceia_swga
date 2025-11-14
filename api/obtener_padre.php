<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    // Se espera recibir el ID del padre directamente.
    $padre_id = $_GET['id'] ?? null;
    if (!$padre_id) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }

    // Obtener los datos del padre usando el ID proporcionado.
    $stmt_padre = $conn->prepare("SELECT * FROM padres WHERE padre_id = :id_padre");
    $stmt_padre->execute([':id_padre' => $padre_id]);
    $padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);

    if (!$padre) {
        // Si no se encuentra, devolver null para que el frontend lo maneje.
        echo json_encode(null);
        exit;
    }

    echo json_encode($padre);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>