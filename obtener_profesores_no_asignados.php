<?php
require_once "../conn/conexion.php";

$periodo_id = $_GET['periodo_id'] ?? null;
$response = ['error' => 'No se proporcionó un ID de período.'];

if ($periodo_id) {
    try {
        $sql = "SELECT id, nombre_completo
                FROM profesores
                WHERE id NOT IN (
                    SELECT profesor_id FROM profesor_periodo WHERE periodo_id = :periodo_id
                )
                ORDER BY nombre_completo ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':periodo_id' => $periodo_id]);
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = $profesores;

    } catch (PDOException $e) {
        $response = ['error' => 'Error de base de datos: ' . $e->getMessage()];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
