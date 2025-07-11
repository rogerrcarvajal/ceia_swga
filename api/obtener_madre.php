<?php
// 1. Establecer la cabecera JSON al principio de todo.
header('Content-Type: application/json');

// 2. Incluir la configuración
require_once __DIR__ . '/../src/config.php';

// 3. Preparar una estructura de respuesta estándar
$response = [
    'status' => 'error',
    'data' => null,
    'message' => 'No se pudo procesar la solicitud.'
];

try {
    $madreid = $_GET['madre_id'] ?? null;
    if (!$madreid) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }
    // CORRECCIÓN: Se busca por la clave primaria 'id' de la tabla 'padres'.
    $stmt = $conn->prepare("SELECT * FROM padres WHERE madre_id = :madre_id");
    $stmt->execute([':madre_id' => $padreid]);
    $madre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($madre) {
        $response['status'] = 'exito';
        $response['data'] = $madre;
    } else {
        $response['message'] = 'No se encontró información para la madre con el ID proporcionado.';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}
echo json_encode($response['data'] ?? ['error' => $response['message']]);
?>