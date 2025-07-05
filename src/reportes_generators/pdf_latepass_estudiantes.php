<?php
require "../conn/conexion.php";
require "../lib/fpdf.php";

$estudiante_id = $_GET['id'] ?? 0;

// Obtener datos del estudiante
$stmt = $conn->prepare("SELECT nombre_completo, grado_ingreso FROM estudiantes WHERE id = :id");
$stmt->execute([':id' => $estudiante_id]);
$est = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$est) {
    die("Estudiante no encontrado.");
}

// Obtener llegadas tarde de la semana
$stmt = $conn->prepare("SELECT TO_CHAR(fecha_registro, 'DD/MM/YYYY') AS fecha, hora_llegada 
                        FROM llegadas_tarde 
                        WHERE estudiante_id = :id 
                        ORDER BY fecha_registro ASC");
$stmt->execute([':id' => $estudiante_id]);
$llegadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Late-Pass CEIA"), 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Estudiante: ', 0, 0);
$pdf->Cell(100, 10, utf8_decode($est['nombre_completo']), 0, 1);

$pdf->Cell(50, 10, 'Grado: ', 0, 0);
$pdf->Cell(100, 10, $est['grado_ingreso'], 0, 1);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Fecha', 1);
$pdf->Cell(60, 10, 'Hora de Llegada', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);

if (count($llegadas) > 0) {
    foreach ($llegadas as $llegada) {
        $pdf->Cell(60, 10, $llegada['fecha'], 1);
        $pdf->Cell(60, 10, $llegada['hora_llegada'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(120, 10, 'No hay llegadas tarde registradas.', 1, 1, 'C');
}

$pdf->Output("I", "LatePass_" . $est['nombre_completo'] . ".pdf");
?>