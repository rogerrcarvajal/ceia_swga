<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// --- 1. OBTENER DATOS DEL PERÍODO ACTIVO ---
$periodo_stmt = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
if ($periodo_stmt->rowCount() === 0) { die("Error: No hay un período escolar activo."); }
$periodo = $periodo_stmt->fetch(PDO::FETCH_ASSOC);
$periodo_id = $periodo['id'];
$nombre_periodo = $periodo['nombre_periodo'];

// --- Obtener Personal ---
$profesores_sql = "SELECT p.nombre_completo, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :periodo_id ORDER BY pp.posicion, p.nombre_completo";
$profesores_stmt = $conn->prepare($profesores_sql);
$profesores_stmt->execute([':periodo_id' => $periodo_id]);
$profesores = $profesores_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Obtener Estudiantes ---
$estudiantes_sql = "SELECT nombre_completo, apellido_completo, grado_ingreso FROM estudiantes WHERE activo = TRUE AND periodo_id = :periodo_id ORDER BY grado_ingreso, apellido_completo, nombre_completo";
$estudiantes_stmt = $conn->prepare($estudiantes_sql);
$estudiantes_stmt->execute([':periodo_id' => $periodo_id]);
$estudiantes_result = $estudiantes_stmt->fetchAll(PDO::FETCH_ASSOC);
$estudiantes_por_grado = [];
foreach ($estudiantes_result as $estudiante) {
    $grado = $estudiante['grado_ingreso'];
    $estudiantes_por_grado[$grado][] = $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'];
}

// --- 2. CLASE PDF PERSONALIZADA PARA EL ROSTER ---
class PDF_Roster extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Asociacion Civil Centro Educativo Internacional Anzoategui', 0, 1, 'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

// --- 3. GENERACIÓN DEL DOCUMENTO ---
$pdf = new PDF_Roster('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'Roster ' . $nombre_periodo, 0, 1, 'C');
$pdf->Ln(10);

// Tabla de Personal
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Personal Administrativo y Docente', 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(0, 74, 143); // Azul oscuro
$pdf->SetTextColor(255); // Blanco
$pdf->Cell(95, 8, 'Especialidad / Cargo', 1, 0, 'C', true);
$pdf->Cell(95, 8, 'Nombre Completo', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0);
foreach ($profesores as $profesor) {
    $pdf->Cell(95, 7, utf8_decode($profesor['posicion']), 1);
    $pdf->Cell(95, 7, utf8_decode($profesor['nombre_completo']), 1, 1);
}
$pdf->Ln(10);

// Listado de Estudiantes (en columnas)
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Listado de Estudiantes por Grado', 0, 1);
$pdf->SetFont('Arial', '', 10);
// Aquí iría la lógica más compleja para crear las 3 columnas, pero por simplicidad
// por ahora lo haremos en una sola columna para asegurar la funcionalidad.

foreach ($estudiantes_por_grado as $grado => $estudiantes) {
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0, 8, utf8_decode($grado), 1, 1, 'L');
    $pdf->SetFont('Arial','',10);
    foreach ($estudiantes as $nombre_completo) {
        $pdf->Cell(0, 7, '  - ' . utf8_decode($nombre_completo), 'LR', 1);
    }
    $pdf->Cell(0,1,'','T'); // Linea inferior
    $pdf->Ln(4);
}


$pdf->Output('I', 'Roster_' . $nombre_periodo . '.pdf');
?>