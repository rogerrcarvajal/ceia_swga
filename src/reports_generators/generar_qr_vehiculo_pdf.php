<?php
session_start();
// 1. Verificación de Seguridad
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado. Debe iniciar sesión.');
}

// 2. Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php';

// 3. Obtener el ID del vehículo
$vehiculo_id = $_GET['id'] ?? 0;
if (!$vehiculo_id) {
    die('Error: ID de vehículo no proporcionado.');
}

// CONSULTA: Obtener datos del vehículo y estudiante asociado
$stmt = $conn->prepare("
    SELECT v.placa, v.modelo, v.color,
           e.nombre_completo, e.apellido_completo
    FROM vehiculos v
    JOIN estudiantes e ON v.estudiante_id = e.id
    WHERE v.id = :id
");
$stmt->execute([':id' => $vehiculo_id]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehiculo) {
    die('Error: Vehículo no encontrado.');
}

// 4. Generar imagen QR temporal
$qr_temp_file = __DIR__ . '/temp_qr.png';
QRcode::png($vehiculo_id, $qr_temp_file, 'L', 10, 2);

// 5. Clase PDF Personalizada
class Generar_qr_pdf extends FPDF {
    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Centro Educativo Internacional Anzoategui', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 100, 0);
        $this->Cell(0, 5, 'Control de Acceso Vehicular', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-20);
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/color_line.png', 10, $this->GetY(), 190);
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Av. José Antonio Anzoátegui, Km 98 - Anaco, Edo Anzoátegui 6003, Venezuela - +58 282 422 2683'), 0, 1, 'C');
        $this->Cell(0, 5, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, $title, 1, 1, 'L', true);
        $this->Ln(2);
    }

    function DataRow($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 7, utf8_decode($label), 'B', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 7, utf8_decode($value ?? 'N/A'), 'B', 1);
    }
}

// 6. Generación del PDF
$pdf = new Generar_qr_pdf('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'QR de Acceso Vehicular', 0, 1, 'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, 'Creado por: ' . $_SESSION['usuario']['username'], 0, 1, 'C');
$pdf->Ln(5);

// Sección de Datos del Vehículo
$pdf->SectionTitle('Datos del Vehículo');
$pdf->DataRow('Placa:', $vehiculo['placa']);
$pdf->DataRow('Modelo:', $vehiculo['modelo']);
$pdf->DataRow('Color:', $vehiculo['color']);
$pdf->DataRow('Vinculado a:', $vehiculo['nombre_completo'] . ' ' . $vehiculo['apellido_completo']);

// Insertar QR
$pdf->Image($qr_temp_file, 65, 100, 80, 80);

// 7. Enviar PDF y limpiar
$nombre_archivo = 'QR_Vehiculo_' . str_replace(' ', '_', $vehiculo['placa']) . '.pdf';
$pdf->Output('I', $nombre_archivo);
unlink($qr_temp_file);
?>