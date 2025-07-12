<?php
session_start();
if (!isset($_SESSION['usuario'])) { exit('Acceso denegado.'); }

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../lib/fpdf.php';

if (!isset($_GET['id'])) { die('ID de estudiante no proporcionado.'); }
$id = $_GET['id'];

// --- OBTENER TODOS LOS DATOS VINCULADOS ---
$est = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
$est->execute([':id' => $id]);
$estudiante = $est->fetch(PDO::FETCH_ASSOC);

// ... (Aquí irían las consultas para padre, madre y ficha médica, como las que ya hicimos)

// --- CLASE PDF PERSONALIZADA PARA LA PLANILLA ---
class PlanillaPDF extends FPDF {
    // ... (Puedes añadir Header y Footer si lo deseas) ...
}

$pdf = new PlanillaPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'PLANILLA DE INSCRIPCION', 0, 1, 'C');
$pdf->Ln(10);

// Sección Datos del Estudiante
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DATOS DEL ESTUDIANTE', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, 'Nombre Completo:', 1, 0);
$pdf->Cell(0, 7, utf8_decode($estudiante['nombre_completo']), 1, 1);
// ... y así sucesivamente con todos los campos ...

// ... (Sección Padre, Madre, Ficha Médica) ...

$pdf->Output('I', 'Planilla_Inscripcion_'. $estudiante['apellido_completo'] .'.pdf');
?>