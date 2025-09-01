<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

date_default_timezone_set('America/Caracas');

$semana = $_GET['semana'] ?? '';
$vehiculo_id = $_GET['vehiculo_id'] ?? 0;

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

$sql .= " ORDER BY rv.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre estudiante para título
$nombre_estudiante = isset($datos[0]) ? $datos[0]['nombre_completo'] . ' ' . $datos[0]['apellido_completo'] : 'Estudiante';

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode("Movimiento de Vehículo del Estudiante: {$nombre_estudiante}"), 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 8, 'Fecha', 1);
$pdf->Cell(40, 8, 'Placa', 1);
$pdf->Cell(45, 8, 'Modelo', 1);
$pdf->Cell(45, 8, 'Hora Entrada', 1);
$pdf->Cell(45, 8, 'Hora Salida', 1);
$pdf->Cell(50, 8, 'Registrado Por', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

foreach ($datos as $row) {
    $pdf->Cell(35, 8, $row['fecha'], 1);
    $pdf->Cell(40, 8, $row['placa'], 1);
    $pdf->Cell(45, 8, $row['modelo'], 1);
    $pdf->Cell(45, 8, $row['hora_entrada'] ?? '-', 1);
    $pdf->Cell(45, 8, $row['hora_salida'] ?? '-', 1);
    $pdf->Cell(50, 8, $row['registrado_por'], 1);
    $pdf->Ln();
}

$pdf->Output('I', "Movimiento del Vehículo del Estudiante {$nombre_estudiante}.pdf");