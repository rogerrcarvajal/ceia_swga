<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin', 'consulta'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

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

class PDF extends FPDF
{
    private $periodo_nombre;

    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4', $periodo_nombre = 'N/A')
    {
        parent::__construct($orientation, $unit, $size);
        $this->periodo_nombre = $periodo_nombre;
    }

    function Header()
    {
        $this->Image('c:/xampp/htdocs/ceia_swga/public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(110, 10, utf8_decode('Reporte de Estudiantes'), 0, 0, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(110, 10, utf8_decode('Periodo Activo: ' . $this->periodo_nombre), 0, 0, 'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_id = $periodo_activo['id'] ?? 0;

if (!$periodo_id) {
    die(utf8_decode("No hay un período escolar activo."));
}

$stmt_est = $conn->prepare("
    SELECT 
        e.nombre_completo, 
        e.apellido_completo, 
        ep.grado_cursado,
        p.padre_nombre || ' ' || p.padre_apellido AS nombre_padre,
        m.madre_nombre || ' ' || m.madre_apellido AS nombre_madre,
        p.padre_celular,
        m.madre_celular,
        p.padre_email,
        m.madre_email
    FROM estudiantes e
    JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
    LEFT JOIN padres p ON e.padre_id = p.padre_id
    LEFT JOIN madres m ON e.madre_id = m.madre_id
    WHERE ep.periodo_id = :pid 
    ORDER BY e.apellido_completo, e.nombre_completo
");
$stmt_est->execute([':pid' => $periodo_id]);
$estudiantes = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF('L', 'mm', 'Letter', $periodo_activo['nombre_periodo']);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Ln(5);

// Encabezados
$pdf->Cell(60, 7, utf8_decode('Nombre Completo'), 1);
$pdf->Cell(25, 7, utf8_decode('Grado'), 1);
$pdf->Cell(60, 7, utf8_decode('Padre / Madre'), 1);
$pdf->Cell(40, 7, utf8_decode('Teléfonos'), 1);
$pdf->Cell(70, 7, utf8_decode('Email'), 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($estudiantes as $e) {
    $nombre_completo = utf8_decode($e['apellido_completo'] . ', ' . $e['nombre_completo']);
    $padres = utf8_decode(($e['nombre_padre'] ?: 'N/A') . ' / ' . ($e['nombre_madre'] ?: 'N/A'));
    $telefonos = ($e['padre_celular'] ?: 'N/A') . ' / ' . ($e['madre_celular'] ?: 'N/A');
    $emails = ($e['padre_email'] ?: 'N/A') . ' / ' . ($e['madre_email'] ?: 'N/A');

    $pdf->Cell(60, 6, $nombre_completo, 1);
    $pdf->Cell(25, 6, utf8_decode($e['grado_cursado']), 1);
    $pdf->Cell(60, 6, $padres, 1);
    $pdf->Cell(40, 6, $telefonos, 1);
    $pdf->Cell(70, 6, $emails, 1);
    $pdf->Ln();
}

$nombre_archivo = "lista_estudiantes_" . sanitize_filename($periodo_activo['nombre_periodo']) . ".pdf";
$pdf->Output('D', $nombre_archivo);