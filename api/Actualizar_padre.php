<?php
session_start();
require_once __DIR__ . '/../src/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $padre_id = $_POST['padre_id'] ?? null;

    if (!$padre_id) {
        echo json_encode(['error' => 'ID del padre no proporcionado']);
        exit;
    }

    // Obtener la cédula actual desde la base de datos
    $stmt = $conn->prepare("SELECT padre_cedula_pasaporte FROM padres WHERE padre_id = :id");
    $stmt->execute([':id' => $padre_id]);
    $padre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$padre) {
        echo json_encode(['error' => 'Padre no encontrado']);
        exit;
    }

    // Ignorar la cédula enviada desde el formulario y usar la de la BD
    $cedula = $padre['padre_cedula_pasaporte'];

                $sql = "UPDATE padres SET 
                        padre_nombre = :padre_nombre, padre_apellido = :padre_apellido,
                        padre_fecha_nacimiento = :padre_fecha_nacimiento,
                        padre_nacionalidad = :padre_nacionalidad, padre_idioma = :padre_idioma,
                        padre_profesion = :padre_profesion, padre_empresa = :padre_empresa,
                        padre_telefono_trabajo = :padre_telefono_trabajo, padre_celular = :padre_celular,
                        padre_email = :padre_email
                    WHERE padre_id = :id_where"; // Usamos la Primary Key

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id_where' => $padre_id,
                ':padre_nombre' => $_POST['padre_nombre'] ?? '',
                ':padre_apellido' => $_POST['padre_apellido'] ?? '',
                ':padre_fecha_nacimiento' => $_POST['padre_fecha_nacimiento'] ?: null,
                ':padre_nacionalidad' => $_POST['padre_nacionalidad'] ?? '',
                ':padre_idioma' => $_POST['padre_idioma'] ?? '',
                ':padre_profesion' => $_POST['padre_profesion'] ?? '',
                ':padre_empresa' => $_POST['padre_empresa'] ?? '',
                ':padre_telefono_trabajo' => $_POST['padre_telefono_trabajo'] ?? '',
                ':padre_celular' => $_POST['padre_celular'] ?? '',
                ':padre_email' => $_POST['padre_email'] ?? ''
            ]);
           
    echo json_encode(['success' => 'Datos del padre actualizados correctamente']);
}
?>