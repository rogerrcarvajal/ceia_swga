<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php'; // Ruta a la librería

$estudiante_id = $_GET['id'] ?? 0;
// ... (Obtener datos del estudiante) ...

// Generar la imagen del QR temporalmente
$qr_temp_file = __DIR__ . '/temp_qr.png';
QRcode::png($estudiante_id, $qr_temp_file, 'QR_ECLEVEL_L', 10);

// Crear PDF e insertar el QR
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10, 'Carnet de Estudiante', 0, 1, 'C');
// ... (Añadir nombre y foto del estudiante) ...
$pdf->Image($qr_temp_file, 65, 80, 80, 80); // Posicionar el QR
$pdf->Output();
unlink($qr_temp_file); // Borrar el archivo temporal
?>