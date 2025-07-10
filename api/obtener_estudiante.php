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
        throw new InvalidArgumentException('ID de estudiante no proporcionado.');
    }

    $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($estudiante) {
        $response['status'] = 'exito';
        $response['data'] = $estudiante;
        $response['message'] = 'Estudiante encontrado.';
    } else {
        // No es un error, simplemente no se encontró.
        $response['status'] = 'error';
        $response['message'] = 'Estudiante no encontrado con el ID proporcionado.';
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