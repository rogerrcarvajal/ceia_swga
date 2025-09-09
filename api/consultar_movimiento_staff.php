<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$semana = isset($_GET['semana']) ? $_GET['semana'] : '';
$staff_id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;

if (!$semana) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor seleccione una semana.']);
    exit;
}

try {
    $week_start = date('Y-m-d', strtotime($semana));
    // The week in the input is ISO-8601 week, which starts on Monday.
    // So we need to go to Sunday for the end of the week.
    $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));

    // This query will generate a row for each staff member for each day of the selected week.
    // It correctly finds the first entry and last exit, and if both are NULL, the person is marked absent.
    $sql = "
        WITH staff_list AS (
            SELECT id, nombre_completo, apellido_completo FROM staff
            WHERE (:staff_id = 0 OR id = :staff_id)
        ),
        date_series AS (
            SELECT generate_series(:week_start::date, :week_end::date, '1 day'::interval) as fecha
        )
        SELECT
            s.nombre_completo || ' ' || s.apellido_completo as nombre_completo,
            ds.fecha::date as fecha,
            MIN(CASE WHEN m.tipo_movimiento = 'ENTRADA' THEN m.hora_registro END) as hora_entrada,
            MAX(CASE WHEN m.tipo_movimiento = 'SALIDA' THEN m.hora_registro END) as hora_salida,
            (CASE 
                WHEN MIN(m.hora_registro) IS NULL THEN true
                ELSE false 
            END) as ausente
        FROM date_series ds
        CROSS JOIN staff_list s
        LEFT JOIN movimientos_staff m ON s.id = m.staff_id AND ds.fecha = m.fecha_registro
        GROUP BY s.nombre_completo, s.apellido_completo, ds.fecha
        ORDER BY ds.fecha DESC, nombre_completo ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':week_start' => $week_start,
        ':week_end' => $week_end,
        ':staff_id' => $staff_id
    ]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'ok', 'data' => $resultados]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
