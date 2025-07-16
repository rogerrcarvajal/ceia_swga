<?php
session_start();
// 1. Verificación de Seguridad: Si no hay sesión, no se puede generar el reporte.
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado. Debe iniciar sesión.');
}

// 2. Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php'; // Ruta a la librería

// Obtener el período escolar activo
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nombre_del_periodo = $periodo_activo['nombre_periodo'] ?? 'No Definido';

$estudiante_id = $_GET['id'] ?? 0;
// ... (Obtener datos del estudiante) ...

// CONSULTA 1: Estudiante
$stmt_est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$stmt_est->execute([':id' => $estudiante_id]);
$estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    die('Error: Estudiante no encontrado.');
}

// Generar la imagen del QR temporalmente
$qr_temp_file = __DIR__ . '/temp_qr.png';
QRcode::png($estudiante_id, $qr_temp_file, 'QR_ECLEVEL_L', 10);

// --- CLASE PDF PERSONALIZADA ---
class Generar_qr_pdf extends FPDF
{
    private $nombre_periodo;

    function __construct($orientation, $unit, $size, $periodo_nombre) {
        parent::__construct($orientation, $unit, $size);
        $this->nombre_periodo = $periodo_nombre;
    }

    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Centro Educativo Internacional Anzoategui', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 100, 0); // Verde oscuro
        $this->Cell(0, 5, 'Periodo Escolar Activo: ' . $this->nombre_periodo, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0); // Restaurar a negro
        $this->Ln(10);
    }

    function Footer() {
        // Posición a 2 cm del final
        $this->SetY(-20);
        // Imagen de línea de colores
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/color_line.png', 10, $this->GetY(), 190);
        
        $this->SetY(-15); // Posición a 1.5 cm del final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Av. José Antonio Anzoátegui, Km 98 - Anaco, Edo Anzoátegui 6003, Venezuela - +58 282 422 2683'), 0, 1, 'C');
        $this->Cell(0, 5, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
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

// --- GENERACIÓN DEL DOCUMENTO PDF ---
$pdf = new Generar_qr_pdf('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'CONTROL DE ACCESO LATE-PASS', 0, 1, 'C');
$pdf->Cell(0, 10, 'Creado por: ' . $_SESSION['usuario']['username'], 0, 1, 'C');
$pdf->Ln(5);

// Sección 1: Datos del Estudiante
$pdf->SectionTitle('Datos del Estudiante');
$pdf->DataRow('Nombre Completo:', $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo']);
$pdf->DataRow('Grado para el Periodo Activo:', $asignacion_activa['grado_cursado'] ?? 'No asignado');


// Crear PDF e insertar el QR
// ... (Añadir nombre y foto del estudiante) ...
$pdf->Image($qr_temp_file, 65, 95, 80, 80); // Posicionar el QR
$pdf->Output();
unlink($qr_temp_file); // Borrar el archivo temporal

// --- Nombre del archivo de salida ---
$nombre_archivo = 'CODIGO_QR_' . str_replace(' ', '_', $estudiante['nombre_completo'] . '_' . $estudiante['apellido_completo']) . '.pdf';
$pdf->Output('I', $nombre_archivo);
?>;
