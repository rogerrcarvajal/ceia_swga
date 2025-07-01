<?php
require_once "conn/conexion.php";

// Validar que se recibe el ID del padre
if (empty($_POST['padre_id'])) {
    echo "Error: No se ha proporcionado el ID del padre para actualizar.";
    exit;
}

$sql = "UPDATE padres SET 
            nombre = :nombre, 
            apellido = :apellido, 
            fecha_nacimiento = :fecha_nacimiento,
            cedula_pasaporte = :cedula_pasaporte,
            nacionalidad = :nacionalidad,
            idioma = :idioma,
            profesion = :profesion,
            empresa = :empresa,
            telefono_trabajo = :telefono_trabajo,
            celular = :celular, 
            email = :email 
        WHERE id = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':nombre' => $_POST['padre_nombre'],
    ':apellido' => $_POST['padre_apellido'],
    ':fecha_nacimiento' => $_POST['padre_fecha_nacimiento'],
    ':cedula_pasaporte' => $_POST['padre_cedula_pasaporte'],
    ':nacionalidad' => $_POST['padre_nacionalidad'],
    ':idioma' => $_POST['padre_idioma'],
    ':profesion' => $_POST['padre_profesion'],
    ':empresa' => $_POST['padre_empresa'],
    ':telefono_trabajo' => $_POST['padre_telefono_trabajo'],
    ':celular' => $_POST['padre_celular'],
    ':email' => $_POST['padre_email'],
    ':id' => $_POST['padre_id']
]);

// Se devuelve un JSON para un manejo más limpio con Promise.all en el JS
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Datos del padre actualizados.']);
?>
