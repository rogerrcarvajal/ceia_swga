<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin', 'consulta'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

class PDF extends FPDF
{
    private $periodo_nombre;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $periodo_nombre = 'N/A')
    {
        parent::__construct($orientation, $unit, $size);
        $this->periodo_nombre = $periodo_nombre;
    }

    function Header()
    {
        $this->Image('c:/xampp/htdocs/ceia_swga/public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Reporte de Staff Mantenimiento'), 0, 0, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Periodo Activo: ' . $this->periodo_nombre), 0, 0, 'C');
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

$sql_staff = "
    SELECT p.nombre_completo, pp.posicion, p.telefono AS telefono_celular
    FROM profesor_periodo pp
    JOIN profesores p ON pp.profesor_id = p.id
    WHERE pp.periodo_id = :pid AND p.categoria = :categoria
    ORDER BY p.nombre_completo
";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->execute([':pid' => $periodo_id, ':categoria' => 'Staff Mantenimiento']);
$staff = $stmt_staff->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF('P', 'mm', 'Letter', $periodo_activo['nombre_periodo']);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(5);

// Encabezados
$pdf->Cell(90, 7, utf8_decode('Nombre Completo'), 1);
$pdf->Cell(60, 7, utf8_decode('Posición'), 1);
$pdf->Cell(40, 7, utf8_decode('Teléfono'), 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($staff as $s) {
    $pdf->Cell(90, 6, utf8_decode($s['nombre_completo']), 1);
    $pdf->Cell(60, 6, utf8_decode($s['posicion']), 1);
    $pdf->Cell(40, 6, $s['telefono_celular'], 1);
    $pdf->Ln();
}

$nombre_archivo = "lista_staff_mantenimiento_" . str_replace(' ', '_', utf8_decode($periodo_activo['nombre_periodo'])) . ".pdf";
$pdf->Output('D', $nombre_archivo);