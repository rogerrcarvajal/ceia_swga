<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Listado de Profesores CEIA',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,10,'Nombre',1);
$pdf->Cell(40,10,'Cédula',1);
$pdf->Cell(40,10,'Especialidad',1);
$pdf->Cell(50,10,'Teléfono',1);
$pdf->Ln();

$pdf->SetFont('Arial','',11);
$query = $conn->query("SELECT * FROM profesores ORDER BY nombre_completo");
foreach ($query as $row) {
    $pdf->Cell(60,10,utf8_decode($row['nombre_completo']),1);
    $pdf->Cell(40,10,$row['cedula'],1);
    $pdf->Cell(40,10,utf8_decode($row['especialidad']),1);
    $pdf->Cell(50,10,$row['telefono'],1);
    $pdf->Ln();
}

$pdf->Output("I", "Listado_Profesores_CEIA.pdf");