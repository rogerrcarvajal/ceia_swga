<?php
require_once __DIR__ . '/../src/config.php';

// Validar que el ID exista
if (empty($_POST['estudiante_Id'])) {
    echo "Error: ID de estudiante no proporcionado para información de la Madre.";
    exit;
}

// Asignar todas las variables desde _POST
$estudiante_Id = $_POST['estudiante_Id'];
$madre_nombre = $_POST["madre_nombre"] ?? '';
$madre_apellido = $_POST["madre_apellido"] ?? '';
$madre_fecha_nacimiento = $_POST["madre_fecha_nacimiento"] ?? null;
$madre_cedula_pasaporte = $_POST["madre_cedula_pasaporte"] ?? '';
$madre_nacionalidad = $_POST["madre_nacionalidad"] ?? '';
$madre_idioma = $_POST["madre_idioma"] ?? '';
$madre_profesion = $_POST["madre_profesion"] ?? '';
$madre_empresa = $_POST["madre_empresa"] ?? '';
$madre_telefono_trabajo = $_POST["madre_telefono_trabajo"] ?? '';
$madre_celular = $_POST["madre_celular"] ?? '';
$madre_email = $_POST["madre_email"] ?? '';

$sql = "UPDATE madres SET
            madre_nombre = :madre_nombre, 
            madre_apellido = :madre_apellido, 
            madre_fecha_nacimiento = :madre_fecha_nacimiento,
            madre_cedula_pasaporte = :madre_cedula_pasaporte,
            madre_nacionalidad = :madre_nacionalidad,
            madre_idioma = :madre_idioma,
            madre_profesion = :madre_profesion,
            madre_empresa = :madre_empresa,
            madre_telefono_trabajo = :madre_telefono_trabajo,
            madre_celular = :madre_celular, 
            madre_email = :madre_email
        WHERE estudiante_Id = :estudiante_Id";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':madre_nombre' => $madre_nombre,
    ':madre_apellido' => $madre_apellido,
    ':madre_fecha_nacimiento' => $madre_fecha_nacimiento,
    ':madre_cedula_pasaporte' => $madre_cedula_pasaporte,
    ':madre_nacionalidad' => $madre_nacionalidad,
    ':madre_idioma' => $madre_idioma,
    ':madre_profesion' => $madre_profesion,
    ':madre_empresa' => $madre_empresa,
    ':madre_telefono_trabajo' => $madre_telefono_trabajo,
    ':madre_celular' => $madre_celular,
    ':madre_email' => $madre_email,
    ':estudiante_Id' => $estudiante_Id
    ]);

echo "✅ Informacion del Padre actualizada correctamente.";
?>