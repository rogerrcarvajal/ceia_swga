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
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new InvalidArgumentException('ID de estudiante no proporcionado para la ficha médica.');
    }

    $stmt = $conn->prepare("SELECT * FROM padres WHERE padre_id = :id");
    $stmt->execute([':id' => $id]);
    $padre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($padre) {
        $response['status'] = 'exito';
        $response['data'] = $ficha;
        $response['message'] = 'Informacion de padre encontrada.';
    } else {
        // No es un error, simplemente no se encontró.
        $response['status'] = 'error';
        $response['message'] = 'No se encontró una ficha médica para este estudiante.';
    }

} catch (PDOException $e) {
    // Error específico de la base de datos
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    // En un entorno de producción, podrías querer loguear el error en lugar de mostrarlo.
} catch (Exception $e) {
    // Cualquier otro tipo de error
    $response['message'] = 'Error general: ' . $e->getMessage();
}

// 4. Imprimir la respuesta en formato JSON una sola vez al final.
echo json_encode($response['data'] ?? ['error' => $response['message']]);
?>