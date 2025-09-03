<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado.');
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php';

class PDFVehiculo extends FPDF {
    private $periodo;
    function __construct($p) {
        parent::__construct();
        $this->periodo = $p;
    }

    function Header() {
        $this->Image(__DIR__ . '/../../public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode('Centro Educativo Internacional Anzoátegui'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 100, 0);
        $this->Cell(0, 5, utf8_decode('Periodo Escolar Activo: ' . $this->periodo), 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-20);
        $this->Image(__DIR__ . '/../../public/img/color_line.png', 10, $this->GetY(), 190);
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Av. José Antonio Anzoátegui, Km 98 - Anaco, Edo Anzoátegui - +58 282 422 2683'), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function Section($label, $val) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, utf8_decode($label), 'B', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, utf8_decode($val ?? 'N/A'), 'B', 1);
    }
}

$periodo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nombre_periodo = $periodo['nombre_periodo'] ?? 'Indefinido';

$vehiculo_id = $_GET['id'] ?? 0;
if (!$vehiculo_id) {
    $pdf = new PDFVehiculo($nombre_periodo);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Error: ID de vehículo no proporcionado.'), 0, 1, 'C');
    $pdf->Output('D', 'error_no_id.pdf');
    exit();
}

$stmt = $conn->prepare("
    SELECT 
        v.placa, 
        v.modelo,
        e.nombre_completo || ' ' || e.apellido_completo AS propietario
    FROM vehiculos v
    JOIN estudiantes e ON v.estudiante_id = e.id
    WHERE v.id = :id
");
$stmt->execute([':id' => $vehiculo_id]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehiculo) {
    $pdf = new PDFVehiculo($nombre_periodo);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Error: Vehículo no encontrado.'), 0, 1, 'C');
    $pdf->Output('D', 'error_vehiculo_no_encontrado.pdf');
    exit();
}

$qr_temp = __DIR__ . '/temp_qr.png';
QRcode::png('VEH-' . $vehiculo_id, $qr_temp, 'L', 10, 2);

$pdf = new PDFVehiculo($nombre_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('CARNET QR VEHICULAR'), 0, 1, 'C');
$pdf->Ln(8);
$pdf->Section('Placa:', $vehiculo['placa']);
$pdf->Section('Modelo:', $vehiculo['modelo']);
$pdf->Section('Asociado a:', $vehiculo['propietario']);
$pdf->Image($qr_temp, 65, 100, 80, 80);
$pdf->Output('D', 'QR_VEHICULO_' . $vehiculo['placa'] . '.pdf');
unlink($qr_temp);
?>