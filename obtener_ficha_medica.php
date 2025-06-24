<?php
require_once "conn/conexion.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM salud_estudiantil WHERE estudiante_id = :id");
$stmt->execute([':id' => $id]);

$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($ficha);
?>