<?php
require_once __DIR__ . '/../src/config.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM padres WHERE estudiante_id = :estudiante_id");
$stmt->execute([':estudiante_id' => $id]);

$padre = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($padre);
?>