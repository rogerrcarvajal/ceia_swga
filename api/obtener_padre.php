<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';
$response = ['status' => 'error', 'data' => null, 'message' => 'No se pudo procesar la solicitud.'];

try {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }
    // CORRECCIÓN: Se busca por la clave primaria 'id' de la tabla 'padres'.
    $stmt = $conn->prepare("SELECT * FROM padres WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $padre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($padre) {
        $response['status'] = 'exito';
        $response['data'] = $padre;
    } else {
        $response['message'] = 'No se encontró información para el padre con el ID proporcionado.';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}
echo json_encode($response['data'] ?? ['error' => $response['message']]);
?>