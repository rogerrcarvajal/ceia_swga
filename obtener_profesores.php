<?php
require_once "conn/conexion.php";

$periodo_id = $_GET['periodo_id'] ?? null;

if (!$periodo_id) {
    header('Content-Type: application/json', true, 400); // Bad Request
    echo json_encode(['error' => 'ID de período no proporcionado.']);
    exit;
}

try {
    $sql = "SELECT
                pp.id AS asignacion_id,
                p.nombre_completo,
                p.cedula,
                p.telefono,
                pp.posicion,
                pp.homeroom_teacher
            FROM profesor_periodo pp
            JOIN profesores p ON pp.profesor_id = p.id
            WHERE pp.periodo_id = :periodo_id
            ORDER BY p.nombre_completo ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':periodo_id' => $periodo_id]);
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($profesores);

} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>
