<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado.']);
    exit();
}

require_once __DIR__ . '/../src/config.php';

$semana = $_GET['semana'] ?? null;
$vehiculo_id = $_GET['vehiculo_id'] ?? 'todos';

if (!$semana) {
    echo json_encode([]);
    exit();
}

// Convertir "2025-W32" → fecha del lunes correspondiente
$fecha_inicio = date('Y-m-d', strtotime($semana));
$fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));

$params = [
    ':fini' => $fecha_inicio,
    ':ffin' => $fecha_fin,
];

$sql = "
    SELECT
        rv.fecha,
        rv.hora_entrada,
        rv.hora_salida,
        rv.observaciones,
        rv.registrado_por,
        v.placa || ' - ' || v.modelo || ' (' || e.nombre_completo || ' ' || e.apellido_completo || ')' AS descripcion
    FROM registro_vehiculos rv
    JOIN vehiculos v ON rv.vehiculo_id = v.id
    JOIN estudiantes e ON v.estudiante_id = e.id
    WHERE rv.fecha BETWEEN :fini AND :ffin
";

if ($vehiculo_id !== 'todos') {
    $sql .= " AND v.id = :vid";
    $params[':vid'] = $vehiculo_id;
}

$sql .= " ORDER BY rv.fecha DESC, v.placa";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);