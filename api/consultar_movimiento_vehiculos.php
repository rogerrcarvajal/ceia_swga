<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Leer parámetros
$semana = isset($_GET['semana']) ? $_GET['semana'] : '';
$vehiculo_id = isset($_GET['vehiculo_id']) ? intval($_GET['vehiculo_id']) : 0;

try {
    $params = [];
    $where = [];

    if ($semana) {
        $week_start = date('Y-m-d', strtotime($semana));
        $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));
        $where[] = "r.fecha BETWEEN :week_start AND :week_end";
        $params[':week_start'] = $week_start;
        $params[':week_end'] = $week_end;
    }

    if ($vehiculo_id > 0) {
        $where[] = "r.vehiculo_id = :vehiculo_id";
        $params[':vehiculo_id'] = $vehiculo_id;
    }

    $sql = "
        SELECT
            v.placa,
            v.modelo,
            e.nombre_completo,
            e.apellido_completo,
            r.fecha,
            r.hora_entrada,
            r.hora_salida,
            r.registrado_por
        FROM registro_vehiculos r
        JOIN vehiculos v ON r.vehiculo_id = v.id
        JOIN estudiantes e ON v.estudiante_id = e.id
    ";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }

    $sql .= " ORDER BY r.fecha DESC, r.hora_entrada DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'ok', 'data' => $resultados]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}