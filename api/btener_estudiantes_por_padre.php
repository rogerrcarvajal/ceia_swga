<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null; // 'padre' o 'madre'

if (!$id || !in_array($tipo, ['padre', 'madre'])) {
    echo json_encode([]); exit;
}

$columna_id = ($tipo === 'padre') ? 'padre_id' : 'madre_id';
$sql = "SELECT id, nombre_completo, apellido_completo FROM estudiantes WHERE {$columna_id} = :id ORDER BY apellido_completo";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($estudiantes);