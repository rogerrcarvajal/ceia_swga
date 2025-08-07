<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Validación
$input = json_decode(file_get_contents('php://input'), true);
$profesor_id = isset($input['profesor_id']) ? (int)$input['profesor_id'] : 0;
$semana_iso = $input['semana'] ?? '';

if (!$profesor_id || !$semana_iso) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros.']);
    exit;
}

// Obtener fechas
$fecha_inicio = date('Y-m-d', strtotime($semana_iso));
$fecha_fin = date('Y-m-d', strtotime("$fecha_inicio +6 days"));

// Consultar movimientos
$stmt = $conn->prepare("
    SELECT fecha_movimiento, hora_movimiento
    FROM movimientos_staff
    WHERE profesor_id = :pid
      AND fecha_movimiento BETWEEN :inicio AND :fin
    ORDER BY fecha_movimiento, hora_movimiento
");
$stmt->execute([
    ':pid' => $profesor_id,
    ':inicio' => $fecha_inicio,
    ':fin' => $fecha_fin
]);

$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por fecha
$dias = [];
foreach ($movimientos as $m) {
    $fecha = $m['fecha_movimiento'];
    $hora = $m['hora_movimiento'];
    $dias[$fecha][] = $hora;
}

// Preparar respuesta
$resultados = [];

for ($i = 0; $i < 7; $i++) {
    $fecha = date('Y-m-d', strtotime("$fecha_inicio +$i days"));
    $horas = $dias[$fecha] ?? [];

    $entrada = $horas[0] ?? '';
    $salida = (count($horas) > 1) ? end($horas) : '';
    $ausente = empty($horas);

    $resultados[] = [
        'fecha' => $fecha,
        'entrada' => $entrada,
        'salida' => $salida,
        'ausente' => $ausente ? 'Sí' : 'No'
    ];
}

// Enviar respuesta
echo json_encode(['status' => 'exito', 'datos' => $resultados]);
exit;