<?php
require_once __DIR__ . '/../src/config.php';

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // Este es el ID de la asignación (estudiante_periodo.id)
    $field = $_POST['field'] ?? null;
    $value = $_POST['value'] ?? '';

    // Lista blanca de campos permitidos en la tabla de asignación
    $allowed_fields = ['grado_cursado'];

    if ($id && in_array($field, $allowed_fields)) {
        try {
            // Si el valor de homeroom es 'N/A' o vacío, lo guardamos como NULL
            $db_value = ($field === 'grado_cursado' && ($value === 'N/A' || $value === '')) ? null : $value;
            
            $sql = "UPDATE estudiante_periodo SET {$field} = :value WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':value' => $db_value, ':id' => $id]);

            $response = ['status' => 'success', 'message' => 'Asignación actualizada.'];
        } catch (PDOException $e) {
            $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Datos incompletos o campo no permitido para edición.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
