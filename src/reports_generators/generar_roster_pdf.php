<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// 1. OBTENER DATOS DEL PERÍODO ACTIVO
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) { die("Error: No hay un período escolar activo."); }
$periodo_id = $periodo_activo['id'];
$nombre_del_periodo = $periodo_activo['nombre_periodo'];

// 2. OBTENER DATOS DE PERSONAL, ESTUDIANTES Y PROFESORES ASIGNADOS
$stmt_prof = $conn->prepare("SELECT p.nombre_completo, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY pp.posicion, p.nombre_completo");
$stmt_prof->execute([':pid' => $periodo_id]);
$profesores_lista = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);

$stmt_homeroom = $conn->prepare("SELECT p.nombre_completo, pp.homeroom_teacher FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid AND pp.homeroom_teacher IS NOT NULL AND pp.homeroom_teacher != ''");
$stmt_homeroom->execute([':pid' => $periodo_id]);
$maestros_por_grado = [];
foreach($stmt_homeroom->fetchAll(PDO::FETCH_ASSOC) as $teacher) {
    $maestros_por_grado[$teacher['homeroom_teacher']] = $teacher['nombre_completo'];
}

$sql_est = "SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado, e.staff FROM estudiante_periodo ep JOIN estudiantes e ON ep.estudiante_id = e.id WHERE ep.periodo_id = :pid ORDER BY ep.grado_cursado, e.apellido_completo, e.nombre_completo";
$stmt_est = $conn->prepare($sql_est);
$stmt_est->execute([':pid' => $periodo_id]);
$estudiantes_result = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

// 3. PROCESAR DATOS Y CALCULAR TOTALES
$estudiantes_por_grado = [];
$total_estudiantes_staff = 0;
$total_daycare_preschool = 0; $total_elementary = 0; $total_secondary = 0;
$grados_daycare_preschool = ['Daycare', 'Preschool', 'Prekinder 3', 'Prekinder 4', 'Kindergarten'];
$grados_elementary = [ 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5'];
$grados_secondary = ['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

foreach ($estudiantes_result as $estudiante) {
    $grado = $estudiante['grado_cursado'];
    $estudiantes_por_grado[$grado][] = $estudiante;
    if ($estudiante['staff']) { $total_estudiantes_staff++; }
    if (in_array($grado, $grados_daycare_preschool)) { $total_daycare_preschool++; }
    elseif (in_array($grado, $grados_elementary)) { $total_elementary++; }
    elseif (in_array($grado, $grados_secondary)) { $total_secondary++; }
}
$total_estudiantes_regulares = count($estudiantes_result) - $total_estudiantes_staff;

// 2. CLASE PDF PERSONALIZADA
class PDF_Roster extends FPDF
{
    private $nombre_periodo;

    function __construct($orientation, $unit, $size, $periodo_nombre) {
        parent::__construct($orientation, $unit, $size);
        $this->nombre_periodo = $periodo_nombre;
    }

    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/logo_ceia.png', 10, 8, 25);
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
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/ceia_swga/public/img/color_line.png', 10, $this->GetY(), 190);
        
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

// 3. GENERACIÓN DEL DOCUMENTO
$pdf = new PDF_Roster('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'Roster ' . $nombre_del_periodo, 0, 1, 'C');
$pdf->Ln(5);

// Tabla de Personal
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Personal Administrativo y Docente', 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(0, 74, 143); $pdf->SetTextColor(255);
$pdf->Cell(95, 8, 'Especialidad / Cargo', 1, 0, 'C', true);
$pdf->Cell(95, 8, 'Nombre Completo', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10); $pdf->SetTextColor(0);
foreach ($profesores_lista as $profesor) {
    $pdf->Cell(95, 7, utf8_decode($profesor['posicion']), 1);
    $pdf->Cell(95, 7, utf8_decode($profesor['nombre_completo']), 1, 1);
}
$pdf->Ln(10);

// Listado de Estudiantes por Grado
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Listado de Estudiantes por Grado', 0, 1);
if (empty($estudiantes_por_grado)) {
    $pdf->Cell(0, 10, 'No hay estudiantes asignados a este periodo.', 1, 1, 'C');
} else {
    foreach ($estudiantes_por_grado as $grado => $estudiantes) {
        $pdf->SetFont('Arial','B',12);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(0, 8, utf8_decode($grado) . ' (' . count($estudiantes) . ')', 1, 1, 'L', true);
        
        $pdf->SetFont('Arial','BI',9);
        $maestro = $maestros_por_grado[$grado] ?? 'No asignado';
        $pdf->Cell(0, 6, utf8_decode('Prof: ' . $maestro), 'LR', 1);

        $pdf->SetFont('Arial','',10);
        foreach ($estudiantes as $estudiante) {
            $pdf->Cell(0, 7, '   ' . utf8_decode($estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo']), 'LR', 1);

        //$pdf->SetY($y_start); // Volver al inicio de la fila para la siguiente columna

        }
        $pdf->Cell(0,1,'','T');
        $pdf->Ln(4);
    }
}

// --- INICIO DEL NUEVO BLOQUE PARA DIBUJAR LA TABLA DE RESUMEN ---
$pdf->Ln(15); // Espacio antes de la tabla

// Tabla de Resumen
$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(255, 255, 0); // Amarillo
$pdf->Cell(40, 7, 'Staff Students', 1, 0, 'L', true);
$pdf->Cell(20, 7, $total_estudiantes_staff, 1, 1, 'C');
$pdf->SetX($pdf->GetX());
$pdf->SetFillColor(46, 204, 113); // Verde
$pdf->SetTextColor(255);
$pdf->Cell(40, 7, 'Students', 1, 0, 'L', true);
$pdf->SetTextColor(0);
$pdf->Cell(20, 7, $total_estudiantes_regulares, 1, 1, 'C');
$pdf->SetX($pdf->GetX());
$pdf->SetFillColor(220, 220, 220); // Gris
$pdf->Cell(40, 7, 'Total', 1, 0, 'L', true);
$pdf->Cell(20, 7, count($estudiantes_result), 1, 1, 'C');

$pdf->Ln(5);

$pdf->SetX(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, 'Daycare/Preschool/K', 1, 0, 'L');
$pdf->Cell(20, 6, $total_daycare_preschool, 1, 1, 'C');
$pdf->SetX(10);
$pdf->Cell(40, 6, 'Elementary (1-5)', 1, 0, 'L');
$pdf->Cell(20, 6, $total_elementary, 1, 1, 'C');
$pdf->SetX(10);
$pdf->Cell(40, 6, 'Secondary (6-12)', 1, 0, 'L');
$pdf->Cell(20, 6, $total_secondary, 1, 1, 'C');
// --- FIN DEL NUEVO BLOQUE PARA DIBUJAR LA TABLA ---

// Nombre del archivo de salida
$pdf->Output('D', 'Roster_' . str_replace(' ', '_', $nombre_del_periodo) . '.pdf');

?>