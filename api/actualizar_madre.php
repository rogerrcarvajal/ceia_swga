<?php
session_start();
require_once __DIR__ . '/../src/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $madre_id = $_POST['madre_id'] ?? null;

    if (!$madre_id) {
        echo json_encode(['error' => 'ID de la madre no proporcionado']);
        exit;
    }

    $stmt = $conn->prepare("SELECT madre_cedula_pasaporte FROM madres WHERE madre_id = :id");
    $stmt->execute([':id' => $madre_id]);
    $madre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$madre) {
        echo json_encode(['error' => 'Madre no encontrada']);
        exit;
    }

    $cedula = $madre['madre_cedula_pasaporte'];

    // Actualizar solo los campos permitidos
            $sql = "UPDATE madres SET 
                    madre_nombre = :madre_nombre,
                    madre_apellido = :madre_apellido,
                    madre_fecha_nacimiento = :madre_fecha_nacimiento,
                    madre_nacionalidad = :madre_nacionalidad,
                    madre_idioma = :madre_idioma,
                    madre_profesion = :madre_profesion,
                    madre_empresa = :madre_empresa,
                    madre_telefono_trabajo = :madre_telefono_trabajo,
                    madre_celular = :madre_celular,
                    madre_email = :madre_email
                    WHERE madre_id = :id_where"; // Usamos la Primary Key

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id_where' => $madre_id,
                ':madre_nombre' => $_POST['madre_nombre'] ?? '', 
                ':madre_apellido' => $_POST['madre_apellido'] ?? '',
                ':madre_fecha_nacimiento' => $_POST['madre_fecha_nacimiento'] ?: null,
                ':madre_nacionalidad' => $_POST['madre_nacionalidad'] ?? '',
                ':madre_idioma' => $_POST['madre_idioma'] ?? '',
                ':madre_profesion' => $_POST['madre_profesion'] ?? '',
                ':madre_empresa' => $_POST['madre_empresa'] ?? '',
                ':madre_telefono_trabajo' => $_POST['madre_telefono_trabajo'] ?? '',
                ':madre_celular' => $_POST['madre_celular'] ?? '',
                ':madre_email' => $_POST['madre_email'] ?? ''
            ]);
                       
    echo json_encode(['success' => 'Datos de la mdre actualizados correctamente']);
}
?>