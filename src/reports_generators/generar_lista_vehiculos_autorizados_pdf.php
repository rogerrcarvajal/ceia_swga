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
        $this->Image(__DIR__ . '/../../public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Reporte de Vehículos Autorizados'), 0, 0, 'C');
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
if (!$periodo_activo) {
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Error: No hay un período escolar activo.'), 0, 1, 'C');
    $pdf->Output('D', 'error_no_periodo_activo.pdf');
    exit();
}

$stmt_veh = $conn->query("
    SELECT 
        v.placa, 
        v.modelo, 
        v.autorizado,
        e.nombre_completo || ' ' || e.apellido_completo AS nombre_estudiante
    FROM vehiculos v 
    JOIN estudiantes e ON v.estudiante_id = e.id
    ORDER BY nombre_estudiante, v.placa
");
$vehiculos = $stmt_veh->fetchAll(PDO::FETCH_ASSOC);

$pdf = new PDF('P', 'mm', 'Letter', $periodo_activo['nombre_periodo']);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(5);

// Encabezados
$pdf->Cell(80, 7, utf8_decode('Estudiante'), 1);
$pdf->Cell(40, 7, utf8_decode('Placa'), 1);
$pdf->Cell(40, 7, utf8_decode('Modelo'), 1);
$pdf->Cell(30, 7, utf8_decode('Autorizado'), 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($vehiculos as $v) {
    $pdf->Cell(80, 6, utf8_decode($v['nombre_estudiante']), 1);
    $pdf->Cell(40, 6, utf8_decode($v['placa']), 1);
    $pdf->Cell(40, 6, utf8_decode($v['modelo']), 1);
    $pdf->Cell(30, 6, $v['autorizado'] ? utf8_decode('Sí') : 'No', 1, 0, 'C');
    $pdf->Ln();
}

$nombre_archivo = "lista_vehiculos_autorizados_" . str_replace(' ', '_', utf8_decode($periodo_activo['nombre_periodo'])) . ".pdf";
$pdf->Output('D', $nombre_archivo);
?>