<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$periodo_id = $_GET['periodo_id'] ?? 0;
if (!$periodo_id) {
    echo json_encode([]);
    exit();
}

try {
    // Lógica Mejorada:
    // Esta consulta busca dos tipos de estudiantes "asignables":
    // 1. Estudiantes que no están en la tabla `estudiante_periodo` para NINGÚN período (nuevos).
    // 2. Estudiantes que SÍ están en `estudiante_periodo` para el período seleccionado, 
    //    pero cuyo grado es nulo o vacío (asignación incompleta).
    
    $sql = "
        -- Estudiantes completamente nuevos
        SELECT id, nombre_completo, apellido_completo
        FROM estudiantes
        WHERE id NOT IN (SELECT DISTINCT estudiante_id FROM estudiante_periodo)

        UNION

        -- Estudiantes con asignación incompleta en este período
        SELECT e.id, e.nombre_completo, e.apellido_completo
        FROM estudiantes e
        JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
        WHERE ep.periodo_id = :periodo_id AND (ep.grado_cursado IS NULL OR ep.grado_cursado = '')

        ORDER BY apellido_completo, nombre_completo";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':periodo_id' => $periodo_id]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($estudiantes);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>