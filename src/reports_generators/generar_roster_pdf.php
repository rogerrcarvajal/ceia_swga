<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// Obtener período activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$periodo_activo) { die("Error: No hay un período escolar activo."); }
$periodo_id = $periodo_activo['id'];
$nombre_del_periodo = $periodo_activo['nombre_periodo'];

// Obtener Personal asignado al período
$stmt_prof = $conn->prepare("SELECT p.nombre_completo, pp.posicion FROM profesor_periodo pp JOIN profesores p ON pp.profesor_id = p.id WHERE pp.periodo_id = :pid ORDER BY pp.posicion, p.nombre_completo");
$stmt_prof->execute([':pid' => $periodo_id]);
$profesores_lista = $stmt_prof->fetchAll(PDO::FETCH_ASSOC);

// Obtener Estudiantes asignados al período (CON LA NUEVA LÓGICA)
$sql_est = "SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado, e.staff
            FROM estudiante_periodo ep
            JOIN estudiantes e ON ep.estudiante_id = e.id
            WHERE ep.periodo_id = :pid
            ORDER BY ep.grado_cursado, e.apellido_completo, e.nombre_completo";
$stmt_est = $conn->prepare($sql_est);
$stmt_est->execute([':pid' => $periodo_id]);
$estudiantes_result = $stmt_est->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por grado y calcular totales
$estudiantes_por_grado = [];
$total_estudiantes_staff = 0;
foreach ($estudiantes_result as $estudiante) {
    $estudiantes_por_grado[$estudiante['grado_cursado']][] = $estudiante;
    if ($estudiante['staff']) {
        $total_estudiantes_staff++;
    }
}
$total_estudiantes_regulares = count($estudiantes_result) - $total_estudiantes_staff;

// ... (Aquí puedes añadir el cálculo por áreas si lo deseas, como lo hicimos antes)

// --- CLASE PDF PERSONALIZADA (Sin cambios) ---
class PDF_Roster extends FPDF { /* ... El código de la clase va aquí ... */ }

// --- GENERACIÓN DEL DOCUMENTO ---
$pdf = new PDF_Roster('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
// ... (El código para dibujar la tabla de personal y las columnas de estudiantes no cambia) ...

// --- Bloque para DIBUJAR LA TABLA DE RESUMEN (CORREGIDO) ---
$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(255, 255, 0); // Amarillo
$pdf->Cell(40, 7, 'Staff Students', 1, 0, 'L', true);
$pdf->Cell(20, 7, $total_estudiantes_staff, 1, 1, 'C');

$pdf->SetX(10);
$pdf->SetFillColor(46, 204, 113); // Verde
$pdf->SetTextColor(255);
$pdf->Cell(40, 7, 'Students', 1, 0, 'L', true);
$pdf->SetTextColor(0);
$pdf->Cell(20, 7, $total_estudiantes_regulares, 1, 1, 'C');

$pdf->SetX(10);
$pdf->SetFillColor(220, 220, 220); // Gris
$pdf->Cell(40, 7, 'Total', 1, 0, 'L', true);
$pdf->Cell(20, 7, count($estudiantes_result), 1, 1, 'C');

// ... (El resto del código para el resumen por áreas) ...

$pdf->Output('I', 'Roster_' . $nombre_del_periodo . '.pdf');
?>