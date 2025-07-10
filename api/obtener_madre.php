<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['status' => 'error', 'data' => null, 'message' => 'No se pudo procesar la solicitud.'];

try {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new InvalidArgumentException('ID de la madre no proporcionado.');
    }

    $stmt = $conn->prepare("SELECT * FROM madres WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $madre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($madre) {
        $response['status'] = 'exito';
        $response['data'] = $madre;
    } else {
        $response['message'] = 'Padre no encontrado.';
    }

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response['data'] ?? ['error' => $response['message']]);
?>