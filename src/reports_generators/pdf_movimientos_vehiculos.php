<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/pdf_base.php';

date_default_timezone_set('America/Caracas');

$semana = $_GET['semana'] ?? '';
$vehiculo_id = $_GET['vehiculo_id'] ?? 0;

// Título y consulta
$titulo_identificador = 'Todos';
if ($vehiculo_id > 0) {
    $stmt_vehiculo = $conn->prepare("
        SELECT v.placa, e.nombre_completo, e.apellido_completo
        FROM vehiculos v
        JOIN estudiantes e ON v.estudiante_id = e.id
        WHERE v.id = :vehiculo_id
    ");
    $stmt_vehiculo->execute(['vehiculo_id' => $vehiculo_id]);
    $vehiculo_info = $stmt_vehiculo->fetch(PDO::FETCH_ASSOC);
    if ($vehiculo_info) {
        $titulo_identificador = "Placa: " . $vehiculo_info['placa'] . " - Estudiante: " . $vehiculo_info['nombre_completo'] . " " . $vehiculo_info['apellido_completo'];
    }
}

// Armar filtros
$params = [];
$where = [];

if ($semana) {
    $inicio = date('Y-m-d', strtotime($semana));
    $fin = date('Y-m-d', strtotime($inicio . ' +6 days'));
    $where[] = "rv.fecha BETWEEN :start AND :end";
    $params[':start'] = $inicio;
    $params[':end'] = $fin;
}

if ($vehiculo_id > 0) {
    $where[] = "rv.vehiculo_id = :vehiculo_id";
    $params[':vehiculo_id'] = $vehiculo_id;
}

$sql = "
    SELECT v.placa, v.modelo, e.nombre_completo, e.apellido_completo,
           rv.fecha, rv.hora_entrada, rv.hora_salida, rv.registrado_por
    FROM registro_vehiculos rv
    JOIN vehiculos v ON rv.vehiculo_id = v.id
    JOIN estudiantes e ON v.estudiante_id = e.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY e.nombre_completo, v.placa, rv.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Periodo escolar
$stmt_periodo = $conn->prepare("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
$stmt_periodo->execute();
$periodo_result = $stmt_periodo->fetch(PDO::FETCH_ASSOC);
$nombre_periodo = $periodo_result ? $periodo_result['nombre_periodo'] : 'N/A';

$pdf = new LatePassPDF('L', 'mm', 'A4', $nombre_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode("Movimiento de Vehículos"), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode($titulo_identificador), 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);

if ($vehiculo_id <= 0) {
    $pdf->Cell(55, 8, 'Estudiante', 1);
    $pdf->Cell(30, 8, 'Placa', 1);
    $pdf->Cell(35, 8, 'Modelo', 1);
    $pdf->Cell(30, 8, 'Fecha', 1);
    $pdf->Cell(30, 8, 'Hora Entrada', 1);
    $pdf->Cell(30, 8, 'Hora Salida', 1);
    $pdf->Cell(47, 8, 'Registrado Por', 1);
} else {
    $pdf->Cell(35, 8, 'Fecha', 1);
    $pdf->Cell(40, 8, 'Placa', 1);
    $pdf->Cell(45, 8, 'Modelo', 1);
    $pdf->Cell(45, 8, 'Hora Entrada', 1);
    $pdf->Cell(45, 8, 'Hora Salida', 1);
    $pdf->Cell(50, 8, 'Registrado Por', 1);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

foreach ($datos as $row) {
    if ($vehiculo_id <= 0) {
        $pdf->Cell(55, 8, utf8_decode($row['nombre_completo'] . ' ' . $row['apellido_completo']), 1);
        $pdf->Cell(30, 8, $row['placa'], 1);
        $pdf->Cell(35, 8, $row['modelo'], 1);
        $pdf->Cell(30, 8, $row['fecha'], 1);
        $pdf->Cell(30, 8, $row['hora_entrada'] ?? '-', 1);
        $pdf->Cell(30, 8, $row['hora_salida'] ?? '-', 1);
        $pdf->Cell(47, 8, $row['registrado_por'], 1);
    } else {
        $pdf->Cell(35, 8, $row['fecha'], 1);
        $pdf->Cell(40, 8, $row['placa'], 1);
        $pdf->Cell(45, 8, $row['modelo'], 1);
        $pdf->Cell(45, 8, $row['hora_entrada'] ?? '-', 1);
        $pdf->Cell(45, 8, $row['hora_salida'] ?? '-', 1);
        $pdf->Cell(50, 8, $row['registrado_por'], 1);
    }
    $pdf->Ln();
}

$pdf->Output('D', "Movimiento_Vehiculo_{$titulo_identificador}.pdf");