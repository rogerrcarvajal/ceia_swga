<?php
require_once "../conn/conexion.php";

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // Este es el ID de la asignación (profesor_periodo.id)
    $field = $_POST['field'] ?? null;
    $value = $_POST['value'] ?? '';

    // Lista blanca de campos permitidos en la tabla de asignación
    $allowed_fields = ['posicion', 'homeroom_teacher'];

    if ($id && in_array($field, $allowed_fields)) {
        try {
            // Si el valor de homeroom es 'N/A' o vacío, lo guardamos como NULL
            $db_value = ($field === 'homeroom_teacher' && ($value === 'N/A' || $value === '')) ? null : $value;
            
            $sql = "UPDATE profesor_periodo SET {$field} = :value WHERE id = :id";
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
