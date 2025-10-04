<?php
// src/reports_generators/generar_pdf_salida.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php'; // Asegúrate que la ruta a FPDF es correcta

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID de autorización no válido.");
}

// Obtener datos de la autorización y del estudiante
$stmt = $conn->prepare(
    "SELECT a.*, e.nombre_completo, e.apellido_completo
     FROM autorizaciones_salida a
     JOIN estudiantes e ON a.estudiante_id = e.id
     WHERE a.id = :id"
);
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Autorización no encontrada.");
}

// Crear PDF (media página carta: 139.7 x 215.9 mm)
$pdf = new FPDF('P', 'mm', array(139.7, 215.9));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// --- Contenido del PDF ---
// Reutiliza los estilos de tus otros reportes para el encabezado
// Ejemplo:
$pdf->Image(__DIR__.'/../../public/img/logo.png', 10, 8, 20);
$pdf->Cell(0, 10, 'AUTORIZACION DE SALIDA', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 7, 'FECHA:', 0);
$pdf->Cell(0, 7, date("d/m/Y", strtotime($data['fecha_salida'])), 0, 1);
$pdf->Cell(40, 7, 'HORA:', 0);
$pdf->Cell(0, 7, date("g:i a", strtotime($data['hora_salida'])), 0, 1);
$pdf->Ln(5);

$pdf->Cell(40, 7, 'NOMBRE DEL ALUMNO:', 0);
$pdf->Cell(0, 7, utf8_decode($data['apellido_completo'] . ', ' . $data['nombre_completo']), 0, 1);
$pdf->Ln(5);

$pdf->Cell(40, 7, 'RETIRADO POR:', 0);
$pdf->Cell(0, 7, utf8_decode($data['retirado_por_nombre']), 0, 1);
$pdf->Cell(40, 7, 'PARENTESCO:', 0);
$pdf->Cell(0, 7, utf8_decode($data['retirado_por_parentesco']), 0, 1);
$pdf->Ln(5);

$pdf->Cell(40, 7, 'MOTIVO:', 0, 1);
$pdf->MultiCell(0, 5, utf8_decode($data['motivo']), 0, 'L');
$pdf->Ln(20);

$pdf->Cell(0, 10, '___________________________', 0, 1, 'C');
$pdf->Cell(0, 5, 'Firma del Representante', 0, 1, 'C');


// Salida del PDF
$pdf->Output('I', 'Autorizacion_Salida_' . $data['id'] . '.pdf');
?>