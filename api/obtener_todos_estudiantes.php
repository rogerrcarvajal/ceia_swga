<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT id, nombre_completo, apellido_completo FROM estudiantes ORDER BY apellido_completo, nombre_completo");
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($estudiantes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener la lista de estudiantes: ' . $e->getMessage()]);
}
?>