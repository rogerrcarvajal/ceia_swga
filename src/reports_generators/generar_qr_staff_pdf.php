<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

// 2. Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php'; // Ruta a la librería


$profesor_id = $_GET['id'] ?? 0;
if (!$profesor_id) { die('Error: ID de personal no proporcionado.'); }

// Obtener datos del profesor
$stmt = $conn->prepare("SELECT nombre_completo, posicion FROM profesores WHERE id = :id");
$stmt->execute([':id' => $profesor_id]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profesor) { die('Error: Personal no encontrado.'); }

// Generar la imagen del QR. Es importante prefijar el ID para diferenciarlo de un estudiante.
$qr_content = "STAFF:" . $profesor_id;
$qr_temp_file = __DIR__ . '/temp_qr_staff.png';
QRcode::png($qr_content, $qr_temp_file, 'L', 10, 2);

// ... (Clase PDF similar a la de generar_qr_pdf.php) ...

// --- GENERACIÓN DEL DOCUMENTO PDF ---
$pdf = new FPDF('P', 'mm', 'A4'); // Usando FPDF base por simplicidad
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Carnet de Personal - CEIA', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode($profesor['nombre_completo']), 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, utf8_decode($profesor['posicion']), 0, 1, 'C');
$pdf->Image($qr_temp_file, 65, 80, 80, 80);
$pdf->Output('I', 'QR_Staff_' . str_replace(' ', '_', $profesor['nombre_completo']) . '.pdf');
unlink($qr_temp_file);
?>