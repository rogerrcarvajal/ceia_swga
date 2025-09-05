<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$periodo_id = $_GET['periodo_id'] ?? 0;
if (!$periodo_id) {
    echo json_encode([]);
    exit();
}

try {
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