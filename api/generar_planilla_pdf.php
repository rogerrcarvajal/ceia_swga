<?php
session_start();
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/lib/fpdf.php';

// --- Seguridad y Validación ---
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    http_response_code(403);
    die('Acceso denegado.');
}

$salida_id = $_GET['salida_id'] ?? null;
if (!$salida_id) {
    http_response_code(400);
    die('ID de salida no proporcionado.');
}

// --- Obtener Datos de la Base de Datos ---
try {
    $sql = "SELECT 
                a.*, 
                e.nombre_completo as estudiante_nombre, 
                e.apellido_completo as estudiante_apellido,
                u.nombre_usuario as registrado_por_nombre
            FROM autorizaciones_salida a
            JOIN estudiantes e ON a.estudiante_id = e.id
            JOIN usuarios u ON a.registrado_por_usuario_id = u.id
            WHERE a.id = :salida_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['salida_id' => $salida_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        http_response_code(404);
        die('Autorización no encontrada.');
    }
} catch (PDOException $e) {
    http_response_code(500);
    die('Error de base de datos: ' . $e->getMessage());
}

// --- Clase para generar el PDF ---
class PlanillaPDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->Image(__DIR__ . '/../public/img/logo_ceia.png', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Autorizacion de Salida de Estudiante', 0, 0, 'C');
        $this->Ln(20);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    // Tabla de datos
    function FancyTable($header, $data)
    {
        $this->SetFillColor(230, 230, 230);
        $this->SetTextColor(0);
        $this->SetDrawColor(128, 128, 128);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        
        // Cabecera
        $w = array(60, 120);
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        
        // Restaurar fuente y colores
        $this->SetFont('', '');
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(0);

        // Datos
        $fill = false;
        foreach ($data as $row)
        {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->MultiCell($w[1], 6, $row[1], 'LR', 'L', $fill);
            $this->Cell(array_sum($w), 0, '', 'T'); // Línea inferior
            $this->Ln(0);
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// --- Creación del PDF ---
$pdf = new PlanillaPDF('P', 'mm', 'A4');
$pdf->SetTitle("Planilla de Salida - " . $data['estudiante_apellido']);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Información principal
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de la Autorizacion', 0, 1, 'C');
$pdf->Ln(5);

// Formatear los datos para la tabla
$fecha_salida_f = date("d/m/Y", strtotime($data['fecha_salida']));
$hora_salida_f = date("h:i A", strtotime($data['hora_salida']));

$info_data = [
    ['Estudiante', $data['estudiante_apellido'] . ', ' . $data['estudiante_nombre']],
    ['Fecha de Salida', $fecha_salida_f],
    ['Hora de Salida', $hora_salida_f],
    ['Persona que Retira', $data['retirado_por_nombre']],
    ['Parentesco', $data['retirado_por_parentesco']],
    ['Motivo', $data['motivo'] ? $data['motivo'] : 'No especificado.'],
    ['Autorizacion Registrada por', $data['registrado_por_nombre']],
    ['Fecha de Registro', date("d/m/Y h:i A", strtotime($data['fecha_registro']))]
];

$header = ['Campo', 'Informacion'];
$pdf->FancyTable($header, $info_data);

$pdf->Ln(20);

// Sección de firmas
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 10, '_______________________________', 0, 0, 'C');
$pdf->Cell(20);
$pdf->Cell(80, 10, '_______________________________', 0, 1, 'C');
$pdf->Cell(80, 5, 'Firma del Representante que Retira', 0, 0, 'C');
$pdf->Cell(20);
$pdf->Cell(80, 5, 'Firma del Personal Autorizado (CEIA)', 0, 1, 'C');

// Salida del PDF
$pdf->Output('D', 'Planilla_Salida_' . $data['estudiante_apellido'] . '_' . $data['fecha_salida'] . '.pdf');

?>