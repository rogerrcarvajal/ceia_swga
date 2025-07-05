<?php
require_once __DIR__ . '/../src/config.php';

// Validar que el ID exista
if (empty($_POST['estudiante_Id'])) {
    echo "Error: ID de estudiante no proporcionado para información del Padre.";
    exit;
}

// Asignar todas las variables desde _POST
$estudiante_Id = $_POST['estudiante_Id'];
$padre_nombre = $_POST["padre_nombre"] ?? '';
$padre_apellido = $_POST["padre_apellido"] ?? '';
$padre_fecha_nacimiento = $_POST["padre_fecha_nacimiento"] ?? null;
$padre_cedula_pasaporte = $_POST["padre_cedula_pasaporte"] ?? '';
$padre_nacionalidad = $_POST["padre_nacionalidad"] ?? '';
$padre_idioma = $_POST["padre_idioma"] ?? '';
$padre_profesion = $_POST["padre_profesion"] ?? '';
$padre_empresa = $_POST["padre_empresa"] ?? '';
$padre_telefono_trabajo = $_POST["padre_telefono_trabajo"] ?? '';
$padre_celular = $_POST["padre_celular"] ?? '';
$padre_email = $_POST["padre_email"] ?? '';

$sql = "UPDATE padres SET
            padre_nombre = :padre_nombre, 
            padre_apellido = :padre_apellido, 
            padre_fecha_nacimiento = :padre_fecha_nacimiento,
            padre_cedula_pasaporte = :padre_cedula_pasaporte,
            padre_nacionalidad = :padre_nacionalidad,
            padre_idioma = :padre_idioma,
            padre_profesion = :padre_profesion,
            padre_empresa = :padre_empresa,
            padre_telefono_trabajo = :padre_telefono_trabajo,
            padre_celular = :padre_celular, 
            padre_email = :padre_email
        WHERE estudiante_Id = :estudiante_Id";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':padre_nombre' => $padre_nombre,
    ':padre_apellido' => $padre_apellido,
    ':padre_fecha_nacimiento' => $padre_fecha_nacimiento,
    ':padre_cedula_pasaporte' => $padre_cedula_pasaporte,
    ':padre_nacionalidad' => $padre_nacionalidad,
    ':padre_idioma' => $padre_idioma,
    ':padre_profesion' => $padre_profesion,
    ':padre_empresa' => $padre_profesion,
    ':padre_telefono_trabajo' => $padre_telefono_trabajo,
    ':padre_celular' => $padre_celular,
    ':padre_email' => $padre_email,
    ':estudiante_Id' => $estudiante_Id
    ]);

echo "✅ Informacion del Padre actualizada correctamente.";
?>