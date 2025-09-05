<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/pdf_base.php';

date_default_timezone_set('America/Caracas');

$semana = $_GET['semana'] ?? '';
$staff_id = $_GET['staff_id'] ?? 0;

// Obtener datos
$params = [];
$where = [];

if ($semana) {
    $inicio_semana = date('Y-m-d', strtotime($semana));
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));
    $where[] = "es.fecha BETWEEN :start AND :end";
    $params[':start'] = $inicio_semana;
    $params[':end'] = $fin_semana;
}

if ($staff_id > 0) {
    $where[] = "es.profesor_id = :staff_id";
    $params[':staff_id'] = $staff_id;
}

$sql = "
    SELECT p.nombre_completo, es.fecha, es.hora_entrada, es.hora_salida, es.ausente
    FROM entrada_salida_staff es
    JOIN profesores p ON es.profesor_id = p.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY es.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre del profesor para título
$nombre_profesor = $datos[0]['nombre_completo'] ?? 'Personal';

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode("Reporte de Movimiento del Staff: {$nombre_profesor}"), 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 8, 'Fecha', 1);
$pdf->Cell(35, 8, 'Hora Entrada', 1);
$pdf->Cell(35, 8, 'Hora Salida', 1);
$pdf->Cell(30, 8, 'Ausente', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

foreach ($datos as $row) {
    $pdf->Cell(40, 8, $row['fecha'], 1);
    $pdf->Cell(35, 8, $row['hora_entrada'] ?? '-', 1);
    $pdf->Cell(35, 8, $row['hora_salida'] ?? '-', 1);
    $pdf->Cell(30, 8, $row['ausente'] ? 'Sí' : 'No', 1);
    $pdf->Ln();
}

$pdf->Output('I', "Movimiento del Staff {$nombre_profesor}.pdf");