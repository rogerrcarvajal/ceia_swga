<?php
// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ .  '/../lib/fpdf.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt->execute([':id' => $id]);
$est = $stmt->fetch(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Planilla de Inscripción - CEIA"), 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, 'Nombre Completo:', 0, 0);
$pdf->Cell(100, 10, utf8_decode($est['nombre_completo']), 0, 1);

$pdf->Cell(60, 10, 'Fecha de Nacimiento:', 0, 0);
$pdf->Cell(100, 10, $est['fecha_nacimiento'], 0, 1);

$pdf->Cell(60, 10, 'Lugar de Nacimiento:', 0, 0);
$pdf->Cell(100, 10, utf8_decode($est['lugar_nacimiento']), 0, 1);

$pdf->Cell(60, 10, 'Nacionalidad:', 0, 0);
$pdf->Cell(100, 10, utf8_decode($est['nacionalidad']), 0, 1);

$pdf->Cell(60, 10, 'Idioma(s):', 0, 0);
$pdf->Cell(100, 10, utf8_decode($est['idioma']), 0, 1);

$pdf->Cell(60, 10, 'Grado de ingreso:', 0, 0);
$pdf->Cell(100, 10, $est['grado_ingreso'], 0, 1);

$pdf->Cell(60, 10, 'Fecha de Inscripción:', 0, 0);
$pdf->Cell(100, 10, $est['fecha_inscripcion'], 0, 1);

$pdf->Cell(60, 10, 'Recomendado por:', 0, 0);
$pdf->Cell(100, 10, utf8_decode($est['recomendado_por']), 0, 1);

$pdf->Output("I", "Planilla_{$est['nombre_completo']}.pdf");