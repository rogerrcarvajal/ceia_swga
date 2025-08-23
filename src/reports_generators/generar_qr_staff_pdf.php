<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado. Debe iniciar sesión.');
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php';

<<<<<<< HEAD
function sanitize_filename($filename) {
    // Convert to ASCII, transliterating accented characters
    $filename = iconv('UTF-8', 'ASCII//TRANSLIT', $filename);
    // Replace any character that is not a letter, number, underscore, or hyphen with an underscore
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    // Replace multiple underscores with a single underscore
    $filename = preg_replace('/_+/', '_', $filename);
    // Trim underscores from the beginning and end
    $filename = trim($filename, '_');
    // Convert to lowercase
    $filename = strtolower($filename);
    return $filename;
}

=======
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nombre_periodo = $periodo['nombre_periodo'] ?? 'Desconocido';
$periodo_id = $periodo['id'] ?? 0;

$profesor_id = $_GET['id'] ?? 0;
if (!$profesor_id) {
    die("ID de profesor no especificado.");
}

$stmt = $conn->prepare("SELECT nombre_completo FROM profesores WHERE id = :id");
$stmt->execute([':id' => $profesor_id]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profesor) {
    die("Profesor no encontrado.");
}

$stmt2 = $conn->prepare("SELECT posicion FROM profesor_periodo WHERE profesor_id = :pid AND periodo_id = :perid");
$stmt2->execute([':pid' => $profesor_id, ':perid' => $periodo_id]);
$info = $stmt2->fetch(PDO::FETCH_ASSOC);

$qr_temp = __DIR__ . '/temp_qr.png';
QRcode::png('STF-' . $profesor_id, $qr_temp, 'L', 10, 2);

class PDFStaff extends FPDF {
    private $periodo;
    function __construct($p) {
        parent::__construct();
        $this->periodo = $p;
    }
    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/logo_ceia.png', 10, 8, 25);
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
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/color_line.png', 10, $this->GetY(), 190);
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Av. José Antonio Anzoátegui, Km 98 - Anaco, Edo Anzoátegui - +58 282 422 2683'), 0, 1, 'C');
        $this->Cell(0, 5, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function Section($label, $val) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 8, utf8_decode($label), 'B', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, utf8_decode($val ?? 'N/A'), 'B', 1);
    }
}

$pdf = new PDFStaff($nombre_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'CARNET QR STAFF / PROFESOR', 0, 1, 'C');
$pdf->Ln(8);
$pdf->Section('Nombre:', $profesor['nombre_completo']);
$pdf->Section('Posición:', $info['posicion'] ?? 'No Asignada');

$pdf->Image($qr_temp, 65, 100, 80, 80);
<<<<<<< HEAD
$pdf->Output('D', 'QR_' . sanitize_filename($profesor['nombre_completo']) . '.pdf');
=======
$pdf->Output('D', 'QR_' . str_replace(' ', '_', utf8_decode($profesor['nombre_completo'])) . '.pdf');
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
unlink($qr_temp);
?>