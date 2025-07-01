<?php
require_once "conn/conexion.php";

// Validar que se recibe el ID de la madre
if (empty($_POST['madre_id'])) {
    echo "Error: No se ha proporcionado el ID de la madre para actualizar.";
    exit;
}

$sql = "UPDATE madres SET 
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
    ':nombre' => $_POST['madre_nombre'],
    ':apellido' => $_POST['madre_apellido'],
    ':fecha_nacimiento' => $_POST['madre_fecha_nacimiento'],
    ':cedula_pasaporte' => $_POST['madre_cedula_pasaporte'],
    ':nacionalidad' => $_POST['madre_nacionalidad'],
    ':idioma' => $_POST['madre_idioma'],
    ':profesion' => $_POST['madre_profesion'],
    ':empresa' => $_POST['madre_empresa'],
    ':telefono_trabajo' => $_POST['madre_telefono_trabajo'],
    ':celular' => $_POST['madre_celular'],
    ':email' => $_POST['madre_email'],
    ':id' => $_POST['madre_id']
]);

// Se devuelve un JSON para un manejo más limpio con Promise.all en el JS
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Datos de la madre actualizados.']);
?>
