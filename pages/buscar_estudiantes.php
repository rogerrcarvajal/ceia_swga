<?php
require_once "conn/conexion.php";

$busqueda = $_GET['q'] ?? '';

$stmt = $conn->prepare("SELECT id, nombre_completo FROM estudiantes WHERE nombre_completo ILIKE :busqueda ORDER BY nombre_completo ASC");
$stmt->execute([':busqueda' => '%' . $busqueda . '%']);

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($resultados);
?>