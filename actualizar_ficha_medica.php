<?php
require_once "conn/conexion.php";

$estudiante_id = $_POST['estudiante_id'];

$stmt = $conn->prepare("UPDATE salud_estudiantil SET contacto_emergencia = :contacto, telefono1 = :telefono1, telefono2 = :telefono2, observaciones = :observaciones, dislexia = :dislexia, atencion = :atencion, otros = :otros, info_adicional = :info_adicional WHERE estudiante_id = :id");

$stmt->execute([
    ':contacto' => $_POST['contacto_emergencia'],
    ':telefono1' => $_POST['telefono_emergencia1'],
    ':telefono2' => $_POST['telefono_emergencia2'],
    ':observaciones' => $_POST['observaciones'],
    ':dislexia' => isset($_POST['dislexia']) ? 1 : 0,
    ':atencion' => isset($_POST['atencion']) ? 1 : 0,
    ':otros' => isset($_POST['otros']) ? 1 : 0,
    ':info_adicional' => $_POST['info_adicional'],
    ':id' => $estudiante_id
]);

echo "✅ Ficha médica actualizada correctamente.";
?>