<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$periodo_id = $_GET['periodo_id'] ?? 0;
if (!$periodo_id) {
    echo json_encode([]);
    exit();
}

try {
    // La consulta original estaba bien, la mantenemos.
    $sql = "SELECT e.id, e.nombre_completo, e.apellido_completo, ep.grado_cursado
            FROM estudiante_periodo ep
            JOIN estudiantes e ON ep.estudiante_id = e.id
            WHERE ep.periodo_id = :periodo_id
            ORDER BY e.apellido_completo, e.nombre_completo";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([':periodo_id' => $periodo_id]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($estudiantes);

} catch (PDOException $e) {
    // Enviar un error en formato JSON si algo falla
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>