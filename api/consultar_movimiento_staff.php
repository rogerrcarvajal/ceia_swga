<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$semana = isset($_GET['semana']) ? $_GET['semana'] : '';
$staff_id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;

try {
    $params = [];
    $where = [];

    if ($semana) {
        $week_start = date('Y-m-d', strtotime($semana));
        $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));
        $where[] = "es.fecha BETWEEN :week_start AND :week_end";
        $params[':week_start'] = $week_start;
        $params[':week_end'] = $week_end;
    }

    if ($staff_id > 0) {
        $where[] = "es.profesor_id = :staff_id";
        $params[':staff_id'] = $staff_id;
    }

    $sql = "
        SELECT
            p.nombre_completo,
<<<<<<< HEAD
            es.fecha,
=======
            TO_CHAR(es.fecha, 'MM-DD-YYYY') as fecha,
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec
            es.hora_entrada,
            es.hora_salida,
            es.ausente
        FROM entrada_salida_staff es
        JOIN profesores p ON es.profesor_id = p.id
    ";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }

    $sql .= " ORDER BY es.fecha DESC, es.hora_entrada DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'ok', 'data' => $resultados]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}