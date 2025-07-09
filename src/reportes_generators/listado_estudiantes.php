<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Listado de Estudiantes CEIA',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,10,'Nombre',1);
$pdf->Cell(40,10,'Nacimiento',1);
$pdf->Cell(30,10,'Grado',1);
$pdf->Cell(60,10,'Teléfono',1);
$pdf->Ln();

$pdf->SetFont('Arial','',11);
$query = $conn->query("SELECT * FROM estudiantes ORDER BY nombre_completo");
foreach ($query as $row) {
    $pdf->Cell(60,10,utf8_decode($row['nombre_completo']),1);
    $pdf->Cell(40,10,$row['fecha_nacimiento'],1);
    $pdf->Cell(30,10,$row['grado_ingreso'],1);
    $pdf->Cell(60,10,$row['telefono_movil'],1);
    $pdf->Ln();
}

$pdf->Output("I", "Listado_Estudiantes_CEIA.pdf");