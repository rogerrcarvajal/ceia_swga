<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$periodo_id = $_GET['periodo_id'] ?? 0;
if (!$periodo_id) {
    echo json_encode([]);
    exit();
}

try {
<<<<<<< HEAD
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
=======
    // ESTA ES LA LÓGICA CORRECTA:
    // Selecciona todos los estudiantes (e) donde su ID no exista (NOT IN)
    // en la sub-consulta que obtiene todos los IDs de estudiantes que YA ESTÁN
    // en la tabla 'estudiante_periodo' para el período seleccionado.
    $sql = "SELECT e.id, e.nombre_completo, e.apellido_completo 
            FROM estudiantes e
            WHERE e.id NOT IN (
                SELECT ep.estudiante_id FROM estudiante_periodo ep WHERE ep.periodo_id = :periodo_id
            )
            ORDER BY e.apellido_completo, e.nombre_completo";
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad

    $stmt = $conn->prepare($sql);
    $stmt->execute([':periodo_id' => $periodo_id]);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($estudiantes);

} catch (PDOException $e) {
<<<<<<< HEAD
=======
    // Enviar un error en formato JSON si algo falla
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>