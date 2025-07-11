<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['estudiante_id'])) {
        $response['message'] = 'Error: ID de estudiante no proporcionado para Madre.';
        echo json_encode($response);
        exit;
    }

    try {
        $sql = "UPDATE madres SET 
                    madre_nombre = :madre_nombre,
                    madre_apellido = :madre_apellido,
                    madre_fecha_nacimiento = :madre_fecha_nacimiento,
                    madre_cedula_pasaporte = :madre_cedula_pasaporte,
                    madre_nacionalidad = :madre_nacionalidad,
                    idioma = :idioma,
                    madre_profesion = :madre_profesion,
                    madre_empresa = :madre_empresa,
                    madre_telefono_trabajo = :madre_telefono_trabajo,
                    madre_celular = :madre_celular,
                    madre_email = :madre_email
                WHERE estudiante_id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':id' => $_POST['estudiante_id'], 
            ':madre_nombre' => $_POST['madre_nombre'] ?? '', 
            ':madre_apellido' => $_POST['madre_apellido'] ?? '',
            ':madre_fecha_nacimiento' => $_POST['madre_fecha_nacimiento'] ?: null,
            ':madre_cedula_pasaporte' => $_POST['madre_cedula_pasaporte'] ?? '',
            ':madre_nacionalidad' => $_POST['madre_nacionalidad'] ?? '',
            ':idioma' => $_POST['idioma'] ?? '',
            ':madre_profesion' => $_POST['madre_profesion'] ?? '',
            ':madre_empresa' => $_POST['madre_empresa'] ?? '',
            ':madre_telefono_trabajo' => $_POST['madre_telefono_trabajo'] ?? '',
            ':madre_celular' => $_POST['madre_celular'] ?? '',
            ':madre_email' => $_POST['madre_email'] ?? ''
        
        ]);

        $response['status'] = 'exito';
        $response['message'] = '✅ Informacion de la madre actualizada correctamente.';
        
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>