<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECCIÓN: Validar el campo 'madre_id' que viene del formulario.
    if (empty($_POST['madre_id'])) {
        $response['message'] = 'Error: ID de Madre no proporcionado.';
        echo json_encode($response);
        exit;
    }
    try {
        $sql = "UPDATE madres SET 
                    madre_nombre = :madre_nombre, madre_apellido = :madre_apellido,
                    /* ... etc ... */
                WHERE id = :madre_id"; // CORRECCIÓN: La cláusula WHERE usa la columna 'id' de la tabla 'madres'.
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':madre_id' => $_POST['madre_id'],
            ':madre_nombre' => $_POST['madre_nombre'] ?? '',
            ':madre_apellido' => $_POST['madre_apellido'] ?? '',
             /* ... resto de los campos ... */
        ]);
        $response = ['status' => 'exito', 'message' => '✅ Información de la madre actualizada.'];
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}
echo json_encode($response);
?>