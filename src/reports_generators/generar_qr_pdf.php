<?php
session_start();
// 1. Verificación de Seguridad
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado. Debe iniciar sesión.');
}

// 2. Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/php-qrcode/qrlib.php'; // Ruta a la librería

// 3. Obtener datos del período y del estudiante
$periodo_activo = $conn->query("SELECT nombre_periodo, id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nombre_del_periodo = $periodo_activo['nombre_periodo'] ?? 'No Definido';
$periodo_id_activo = $periodo_activo['id'] ?? 0;

$estudiante_id = $_GET['id'] ?? 0;
if (!$estudiante_id) {
    die('Error: ID de estudiante no proporcionado.');
}

// CONSULTA 1: Obtener datos básicos del estudiante
$stmt_est = $conn->prepare("SELECT nombre_completo, apellido_completo FROM estudiantes WHERE id = :id");
$stmt_est->execute([':id' => $estudiante_id]);
$estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

if (!$estudiante) {
    die('Error: Estudiante no encontrado.');
}

// --- ¡CORRECCIÓN AÑADIDA AQUÍ! ---
// CONSULTA 2: Obtener el grado del estudiante para el período activo desde la nueva tabla
$stmt_asig = $conn->prepare(
    "SELECT grado_cursado FROM estudiante_periodo 
     WHERE estudiante_id = :eid AND periodo_id = :pid"
);
$stmt_asig->execute([':eid' => $estudiante_id, ':pid' => $periodo_id_activo]);
$asignacion_activa = $stmt_asig->fetch(PDO::FETCH_ASSOC);
// --- FIN DE LA CORRECCIÓN ---


// 4. Generar la imagen del QR temporalmente
$qr_temp_file = __DIR__ . '/temp_qr.png';
// El QR solo contendrá el ID del estudiante, que es lo único que necesita el lector
QRcode::png($estudiante_id, $qr_temp_file, 'L', 10, 2);


// 5. Clase PDF Personalizada
class Generar_qr_pdf extends FPDF {
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
        $this->SetY(-20);
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/color_line.png', 10, $this->GetY(), 190);
        $this->SetY(-15);
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


// 6. GENERACIÓN DEL DOCUMENTO PDF
$pdf = new Generar_qr_pdf('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'CONTROL DE ACCESO LATE-PASS', 0, 1, 'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, 'Creado por: ' . $_SESSION['usuario']['username'], 0, 1, 'C');
$pdf->Ln(5);

// Sección de Datos del Estudiante
$pdf->SectionTitle('Datos del Estudiante');
$pdf->DataRow('Nombre Completo:', $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo']);
// Se usa la variable '$asignacion_activa' que ahora sí tiene datos
$pdf->DataRow('Grado para el Periodo Activo:', $asignacion_activa['grado_cursado'] ?? 'No asignado');

// Insertar la imagen del QR en el PDF
$pdf->Image($qr_temp_file, 65, 100, 80, 80);

// 7. Enviar el PDF y limpiar
// Nombre del archivo de salida
$nombre_archivo = 'QR_' . str_replace(' ', '_', $estudiante['nombre_completo'] . '_' . $estudiante['apellido_completo']) . '.pdf';
$pdf->Output('I', $nombre_archivo); // 'I' para mostrar en el navegador
unlink($qr_temp_file); // Borrar el archivo de imagen temporal

?>
