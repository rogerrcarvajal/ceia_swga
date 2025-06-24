<?php
require_once "conn/conexion.php";

$id = $_POST['id'];
$nombre = $_POST['nombre_completo'];
$direccion = $_POST['direccion'];
$tel_casa = $_POST['telefono_casa'];
$tel_movil = $_POST['telefono_movil'];
$tel_emergencia = $_POST['telefono_emergencia'];
$grado = $_POST['grado_ingreso'];
$activo = isset($_POST['activo']) ? 1 : 0;

$sql = "UPDATE estudiantes SET nombre_completo = :nombre, direccion = :direccion, telefono_casa = :tel_casa, telefono_movil = :tel_movil, telefono_emergencia = :tel_emergencia, grado_ingreso = :grado, activo = :activo WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':nombre' => $nombre,
    ':direccion' => $direccion,
    ':tel_casa' => $tel_casa,
    ':tel_movil' => $tel_movil,
    ':tel_emergencia' => $tel_emergencia,
    ':grado' => $grado,
    ':activo' => $activo,
    ':id' => $id
]);

echo "✅ Datos actualizados correctamente.";
?>