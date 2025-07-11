<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECCIÓN: El formulario envía el ID en un campo llamado 'id'.
    if (empty($_POST['id'])) {
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
                WHERE id = :id"; // CORRECCIÓN: La cláusula WHERE usa la columna 'id'.
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $_POST['id'],
            ':padre_nombre' => $_POST['padre_nombre'] ?? '',
            ':padre_apellido' => $_POST['padre_apellido'] ?? '',
            ':padre_fecha_nacimiento' => $_POST['padre_fecha_nacimiento'] ?: null,
            ':padre_cedula_pasaporte' => $_POST['padre_cedula_pasaporte'] ?? '',
            ':padre_nacionalidad' => $_POST['padre_nacionalidad'] ?? '',
            ':idioma' => $_POST['idioma'] ?? '',
            ':padre_profesion' => $_POST['padre_profesion'] ?? '',
            ':padre_empresa' => $_POST['padre_empresa'] ?? '',
            ':padre_telefono_trabajo' => $_POST['padre_telefono_trabajo'] ?? '',
            ':padre_celular' => $_POST['padre_celular'] ?? '',
            ':padre_email' => $_POST['padre_email'] ?? ''
        ]);
        $response = ['status' => 'exito', 'message' => '✅ Información del padre actualizada.'];
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}
echo json_encode($response);
?>