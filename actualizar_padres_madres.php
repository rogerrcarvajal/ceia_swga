<?php
require_once "conn/conexion.php";

$padre_id = $_POST['padre_id'];
$madre_id = $_POST['madre_id'];

// Actualizar padre
$stmt = $conn->prepare("UPDATE padres SET nombre = :nombre, apellido = :apellido, celular = :celular, email = :email WHERE id = :id");
$stmt->execute([
    ':nombre' => $_POST['padre_nombre'],
    ':apellido' => $_POST['padre_apellido'],
    ':celular' => $_POST['padre_celular'],
    ':email' => $_POST['padre_email'],
    ':id' => $padre_id
]);

// Actualizar madre
$stmt = $conn->prepare("UPDATE madres SET nombre = :nombre, apellido = :apellido, celular = :celular, email = :email WHERE id = :id");
$stmt->execute([
    ':nombre' => $_POST['madre_nombre'],
    ':apellido' => $_POST['madre_apellido'],
    ':celular' => $_POST['madre_celular'],
    ':email' => $_POST['madre_email'],
    ':id' => $madre_id
]);

echo "✅ Datos de padres/madres actualizados correctamente.";
?>
