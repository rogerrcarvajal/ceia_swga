<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT id, nombre_completo, categoria FROM profesores ORDER BY nombre_completo ASC");
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($staff);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener la lista de staff: ' . $e->getMessage()]);
}
?>