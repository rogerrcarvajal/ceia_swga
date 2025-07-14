<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// --- 1. OBTENCIÓN DE DATOS ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) { die("Error: No hay un período escolar activo."); }
$periodo_id = $periodo_activo['id'];
$nombre_del_periodo = $periodo_activo['nombre_periodo'];

// Obtener Personal
$profesores_stmt = $conn->prepare("SELECT p.nombre_completo, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY pp.posicion, p.nombre_completo");
$profesores_stmt->execute([':pid' => $periodo_id]);
$profesores_lista = $profesores_stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener Homeroom Teachers y agruparlos por grado
$homeroom_teachers_stmt = $conn->prepare("SELECT p.nombre_completo, pp.homeroom_teacher FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid AND pp.homeroom_teacher IS NOT NULL AND pp.homeroom_teacher != ''");
$homeroom_teachers_stmt->execute([':pid' => $periodo_id]);
$homeroom_teachers_result = $homeroom_teachers_stmt->fetchAll(PDO::FETCH_ASSOC);
$maestros_por_grado = [];
foreach($homeroom_teachers_result as $teacher) {
    $maestros_por_grado[$teacher['homeroom_teacher']] = $teacher['nombre_completo'];
}

// Obtener Estudiantes y agruparlos por grado
$estudiantes_stmt = $conn->prepare("SELECT nombre_completo, apellido_completo, grado_ingreso FROM estudiantes WHERE activo = TRUE AND periodo_id = :pid ORDER BY grado_ingreso, apellido_completo, nombre_completo");
$estudiantes_stmt->execute([':pid' => $periodo_id]);
$estudiantes_result = $estudiantes_stmt->fetchAll(PDO::FETCH_ASSOC);
$estudiantes_por_grado = [];
foreach ($estudiantes_result as $estudiante) {
    $estudiantes_por_grado[$estudiante['grado_ingreso']][] = $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'];
}
ksort($estudiantes_por_grado);

// --- INICIO DEL NUEVO BLOQUE DE CÁLCULO DE TOTALES ---
$total_daycare_preschool = 0;
$total_elementary = 0;
$total_secondary = 0;
$total_staff_students = 0;

// Grados que pertenecen a cada categoría
$grados_daycare_preschool = ['Daycare', 'Preschool', 'Prekinder 3', 'Prekinder 4', 'Kindergarten'];
$grados_elementary = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5'];
$grados_secondary = ['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

foreach ($estudiantes_por_grado as $grado => $estudiantes) {
    $count = count($estudiantes);
    if (in_array($grado, $grados_daycare_preschool)) {
        $total_daycare_preschool += $count;
    } elseif (in_array($grado, $grados_elementary)) {
        $total_elementary += $count;
    } elseif (in_array($grado, $grados_secondary)) {
        $total_secondary += $count;
    }
}

$total_staff_students = count($profesores_lista) + count($estudiantes_result);
// --- FIN DEL NUEVO BLOQUE DE CÁLCULO ---

// --- 2. CLASE PDF PERSONALIZADA PARA EL ROSTER ---
class PDF_Roster extends FPDF {
    
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


// --- 3. GENERACIÓN DEL DOCUMENTO ---
$pdf = new PDF_Roster('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'Roster ' . $nombre_del_periodo, 0, 1, 'C');
$pdf->Ln(5);

// Tabla de Personal
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Personal Administrativo y Docente', 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(0, 74, 143);
$pdf->SetTextColor(255);
$pdf->Cell(95, 8, 'Especialidad / Cargo', 1, 0, 'C', true);
$pdf->Cell(95, 8, 'Nombre Completo', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0);
foreach ($profesores_lista as $profesor) {
    $pdf->Cell(95, 7, utf8_decode($profesor['posicion']), 1);
    $pdf->Cell(95, 7, utf8_decode($profesor['nombre_completo']), 1, 1);
}
$pdf->Ln(10);

// Listado de Estudiantes por Grado en COLUMNAS
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Listado de Estudiantes por Grado', 0, 1);
$pdf->SetFont('Arial', '', 10);

$column_width = 63; // Ancho para 3 columnas (190mm / 3)
$margin = 10;
$col = 0; // Columna actual (0, 1, 2)
$y_start = $pdf->GetY();
$y_max_row = $y_start;

if (empty($estudiantes_por_grado)) {
     $pdf->Cell(0, 10, 'No hay estudiantes registrados para este periodo.', 1, 1, 'C');
} else {
    foreach ($estudiantes_por_grado as $grado => $estudiantes) {
        if ($pdf->GetY() + 40 > ($pdf->GetPageHeight() - 20)) { // Estimación de si cabe el siguiente bloque
             $col++;
             if ($col > 2) {
                $pdf->AddPage();
                $col = 0;
                $y_start = $pdf->GetY();
             } else {
                 $pdf->SetY($y_start);
             }
        }

        $pdf->SetX($margin + ($col * $column_width));
        $x_col_start = $pdf->GetX();

        // Título del Grado
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell($column_width, 7, utf8_decode($grado) . ' (' . count($estudiantes) . ')', 1, 1, 'C', true);
        
        // Nombre del Maestro
        $pdf->SetX($x_col_start);
        $pdf->SetFont('Arial', 'BI', 9); // Bold Italic
        $maestro = $maestros_por_grado[$grado] ?? 'N/A';
        $pdf->Cell($column_width, 6, utf8_decode('Prof: ' . $maestro), 'LR', 1, 'C');
        
        // Lista de Estudiantes
        $pdf->SetFont('Arial', '', 9);
        foreach($estudiantes as $estudiante) {
            $pdf->SetX($x_col_start);
            $pdf->Cell($column_width, 5, utf8_decode($estudiante), 'LR', 1, 'L');
        }
        $pdf->SetX($x_col_start);
        $pdf->Cell($column_width, 0, '', 'T', 1); // Cierra el borde de la caja

        if ($pdf->GetY() > $y_max_row) {
            $y_max_row = $pdf->GetY(); // Guardar la altura máxima de la fila actual
        }

        $col++;
        if ($col > 2) {
            $col = 0;
            $pdf->SetY($y_max_row + 5); // Bajar a la siguiente fila
            $y_start = $pdf->GetY();
            $y_max_row = $y_start;
        } else {
            $pdf->SetY($y_start); // Volver al inicio de la fila para la siguiente columna
        }
    }
}

// --- INICIO DEL NUEVO BLOQUE PARA DIBUJAR LA TABLA DE RESUMEN ---
$pdf->Ln(25); // Espacio antes de la tabla

// Posicionar la tabla a la izquierda
$pdf->SetX(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, 'Daycare/Preschool', 1, 0, 'L');
$pdf->Cell(20, 6, $total_daycare_preschool, 1, 1, 'C');
$pdf->SetX(10);
$pdf->Cell(40, 6, 'Elementary (K-5)', 1, 0, 'L');
$pdf->Cell(20, 6, $total_elementary, 1, 1, 'C');
$pdf->SetX(10);
$pdf->Cell(40, 6, 'Secondary (6-12)', 1, 0, 'L');
$pdf->Cell(20, 6, $total_secondary, 1, 1, 'C');

$pdf->Ln(5);

$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(255, 255, 0); // Amarillo para el Staff
$pdf->Cell(40, 7, 'Staff', 1, 0, 'L', true);
$pdf->SetFillColor(240);
$pdf->Cell(20, 7, count($profesores_lista), 1, 1, 'C');

$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(46, 204, 113); // Verde para los Estudiantes
$pdf->SetTextColor(255);
$pdf->Cell(40, 7, 'Students', 1, 0, 'L', true);
$pdf->SetTextColor(0);
$pdf->SetFillColor(240);
$pdf->Cell(20, 7, count($estudiantes_result), 1, 1, 'C');

$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220); // Gris para el Total
$pdf->Cell(40, 7, 'Total', 1, 0, 'L', true);
$pdf->Cell(20, 7, $total_staff_students, 1, 1, 'C');
// --- FIN DEL NUEVO BLOQUE PARA DIBUJAR LA TABLA ---

$pdf->Output('I', 'Roster_' . $nombre_del_periodo . '.pdf');

?>