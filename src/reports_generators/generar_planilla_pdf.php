<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

//Declaracion de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");

$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// --- OBTENER ID DEL ESTUDIANTE ---

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