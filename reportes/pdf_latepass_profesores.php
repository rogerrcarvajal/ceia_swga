<?php
require "../conn/conexion.php";
require "../lib/fpdf.php";

// Obtener datos
$sql = "SELECT p.nombre_completo, m.tipo_movimiento, TO_CHAR(m.fecha_hora, 'DD/MM/YYYY HH24:MI') as fecha_hora
        FROM entrada_salida_profesores m
        JOIN profesores p ON m.profesor_id = p.id
        ORDER BY m.fecha_hora DESC";

$stmt = $conn->query($sql);
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Control de Entrada/Salida - Profesores CEIA"), 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Profesor', 1);
$pdf->Cell(40, 10, 'Movimiento', 1);
$pdf->Cell(60, 10, 'Fecha y Hora', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
foreach ($movimientos as $mov) {
    $pdf->Cell(80, 10, utf8_decode($mov['nombre_completo']), 1);
    $pdf->Cell(40, 10, $mov['tipo_movimiento'], 1);
    $pdf->Cell(60, 10, $mov['fecha_hora'], 1);
    $pdf->Ln();
}

$pdf->Output("I", "Control_Profesores_CEIA.pdf");
?>