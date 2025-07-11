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
    $padreid = $_GET['padre_id'] ?? null;
    if (!$padreid) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }
    // CORRECCIÓN: Se busca por la clave primaria 'id' de la tabla 'padres'.
    $stmt = $conn->prepare("SELECT * FROM padres WHERE padre_id = :padre_id");
    $stmt->execute([':padre_id' => $padreid]);
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