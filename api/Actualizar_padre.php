<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECCIÓN: Validar el campo 'padre_id' que viene del formulario.
    if (empty($_POST['padre_id'])) {
        $response['message'] = 'Error: ID de Padre no proporcionado.';
        echo json_encode($response);
        exit;
    }
    try {
        $sql = "UPDATE padres SET 
                    padre_nombre = :padre_nombre, padre_apellido = :padre_apellido,
                    padre_fecha_nacimiento = :padre_fecha_nacimiento, padre_cedula_pasaporte = :padre_cedula_pasaporte,
                    padre_nacionalidad = :padre_nacionalidad, idioma = :idioma,
                    padre_profesion = :padre_profesion, padre_empresa = :padre_empresa,
                    padre_telefono_trabajo = :padre_telefono_trabajo, padre_celular = :padre_celular,
                    padre_email = :padre_email
                WHERE id = :padre_id"; // CORRECCIÓN: La cláusula WHERE usa la columna 'id' de la tabla 'padres'.
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':padre_id' => $_POST['padre_id'],
            ':padre_nombre' => $_POST['padre_nombre'] ?? '',
            ':padre_apellido' => $_POST['padre_apellido'] ?? '',
            /* ... resto de los campos ... */
            ':padre_email' => $_POST['padre_email'] ?? ''
        ]);
        $response = ['status' => 'exito', 'message' => '✅ Información del padre actualizada.'];
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}
echo json_encode($response);
?>