<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// 1. OBTENER DATOS DEL PERÍODO ACTIVO CON LA NUEVA LÓGICA
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) { die("Error: No hay un período escolar activo."); }
$periodo_id = $periodo_activo['id'];
$nombre_del_periodo = $periodo_activo['nombre_periodo'];

// Obtener Personal asignado
$stmt_prof = $conn->prepare("SELECT p.nombre_completo, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY pp.posicion, p.nombre_completo");
$stmt_prof->execute([':pid' => $periodo_id]);
$profesores_lista = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);

// Obtener Estudiantes asignados
$sql_est = "SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado, e.staff FROM estudiante_periodo ep JOIN estudiantes e ON ep.estudiante_id = e.id WHERE ep.periodo_id = :pid ORDER BY ep.grado_cursado, e.apellido_completo, e.nombre_completo";
$stmt_est = $conn->prepare($sql_est);
$stmt_est->execute([':pid' => $periodo_id]);
$estudiantes_result = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por grado y calcular totales
$estudiantes_por_grado = [];
$total_estudiantes_staff = 0;
foreach ($estudiantes_result as $estudiante) {
    $estudiantes_por_grado[$estudiante['grado_cursado']][] = $estudiante['apellido_completo'] . ', ' . $estudiante['nombre_completo'];
    if ($estudiante['staff']) {
        $total_estudiantes_staff++;
    }
}
$total_estudiantes_regulares = count($estudiantes_result) - $total_estudiantes_staff;
ksort($estudiantes_por_grado);

// 2. CLASE PDF PERSONALIZADA
class PDF_Roster extends FPDF { /* ... (El código de la clase no cambia) ... */ }

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

// Listado de Estudiantes (Diseño Clásico en una columna)
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Listado de Estudiantes por Grado', 0, 1);
if (empty($estudiantes_por_grado)) {
    $pdf->Cell(0, 10, 'No hay estudiantes asignados a este periodo.', 1, 1, 'C');
} else {
    foreach ($estudiantes_por_grado as $grado => $estudiantes) {
        $pdf->SetFont('Arial','B',12);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(0, 8, utf8_decode($grado) . ' (' . count($estudiantes) . ')', 1, 1, 'L', true);
        $pdf->SetFont('Arial','',10);
        foreach ($estudiantes as $nombre_completo) {
            $pdf->Cell(0, 7, '   ' . utf8_decode($nombre_completo), 'LR', 1);
        }
        $pdf->Cell(0,1,'','T');
        $pdf->Ln(4);
    }
}
$pdf->Ln(10);

// Tabla de Resumen
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

// Nombre del archivo de salida
$nombre_archivo = 'Roster_' . str_replace(' ', '_', $nombre_del_periodo) . '.pdf';
$pdf->Output('I', $nombre_archivo);
?>