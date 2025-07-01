<?php
require_once "conn/conexion.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM madres WHERE estudiante_id = :estudiante_id");
$stmt->execute([':estudiante_id' => $id]);

$madre = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($madre);
?>