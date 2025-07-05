<?php
require_once __DIR__ . '/../src/config.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt->execute([':id' => $id]);

$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($estudiante);
?>