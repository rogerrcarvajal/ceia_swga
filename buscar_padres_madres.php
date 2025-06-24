<?php
require_once "conn/conexion.php";

$busqueda = $_GET['q'] ?? '';

$stmt = $conn->prepare("SELECT id, nombre FROM padres WHERE nombre ILIKE :busqueda UNION SELECT id, nombre FROM madres WHERE nombre ILIKE :busqueda ORDER BY nombre ASC");
$stmt->execute([':busqueda' => '%' . $busqueda . '%']);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($resultados);
?>