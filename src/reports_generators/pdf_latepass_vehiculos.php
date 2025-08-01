<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/lib/fpdf.php';

$sql = "SELECT e.nombre_completo, v.placa, v.conductor_nombre, TO_CHAR(v.fecha_hora, 'DD/MM/YYYY HH24:MI') as fecha_hora
        FROM vehiculos_autorizados v
        JOIN estudiantes e ON v.estudiante_id = e.id
        ORDER BY v.fecha_hora DESC";

$stmt = $conn->query($sql);
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Control de Vehículos Autorizados - CEIA"), 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Estudiante', 1);
$pdf->Cell(40, 10, 'Placa', 1);
$pdf->Cell(50, 10, 'Conductor', 1);
$pdf->Cell(40, 10, 'Fecha y Hora', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
foreach ($vehiculos as $v) {
    $pdf->Cell(60, 10, utf8_decode($v['nombre_completo']), 1);
    $pdf->Cell(40, 10, $v['placa'], 1);
    $pdf->Cell(50, 10, utf8_decode($v['conductor_nombre']), 1);
    $pdf->Cell(40, 10, $v['fecha_hora'], 1);
    $pdf->Ln();
}

$pdf->Output("I", "Control_Vehiculos_CEIA.pdf");
?>