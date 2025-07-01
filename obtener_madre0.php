<?php
require_once "conn/conexion.php";

// Validar que se recibe el ID del estudiante
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'ID de estudiante no válido.']);
    exit;
}
$estudiante_id = $_GET['id'];

// Usamos una subconsulta para obtener los datos de la madre a través del estudiante
$sql = "SELECT * FROM madres WHERE id = (SELECT madre_id FROM estudiantes WHERE id = :estudiante_id)";
$stmt = $conn->prepare($sql);
$stmt->execute([':estudiante_id' => $estudiante_id]);

$madre = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra una madre, devolvemos un objeto vacío
if (!$madre) {
    $madre = [];
}

header('Content-Type: application/json');
echo json_encode($madre);
?>
