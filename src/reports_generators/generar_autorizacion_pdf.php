<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// 1. Validar ID de autorización
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Error: ID de autorización no válido o no proporcionado.");
}

// 2. Obtener datos de la base de datos
try {
    $periodo_activo_info = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nombre_periodo = $periodo_activo_info ? $periodo_activo_info['nombre_periodo'] : 'No definido';

    $stmt = $conn->prepare(
        "SELECT a.*, e.nombre_completo, e.apellido_completo, ep.grado_cursado as grado
         FROM autorizaciones_salida a
         JOIN estudiantes e ON a.estudiante_id = e.id
         LEFT JOIN estudiante_periodo ep ON e.id = ep.estudiante_id AND ep.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1)
         WHERE a.id = :id"
    );
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Error: Autorización no encontrada.");
    }
} catch (Exception $e) {
    die('Error al consultar la base de datos: ' . $e->getMessage());
}

// 3. Definir clase PDF con encabezado y pie de página estándar
class PDF extends FPDF
{
    private $periodo_activo;

    function setPeriodoActivo($periodo) {
        $this->periodo_activo = $periodo;
    }

    function Header()
    {
        $this->Image(__DIR__.'/../../public/img/logo_ceia.png', 10, 8, 20);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 8, utf8_decode('Planilla de Autorización de Salida de Estudiantes'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode('Período Escolar: ' . $this->periodo_activo), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        // Se mantiene para el número de página, aunque la imagen principal se añade manualmente.
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 6);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// 4. Crear instancia de PDF y generar contenido
// Tamaño carta
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->setPeriodoActivo($nombre_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// --- Contenido de la Planilla ---

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 20, 'Estudiante:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 20, utf8_decode($data['apellido_completo'] . ', ' . $data['nombre_completo']), 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 1, 'Grado:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 0, utf8_decode($data['grado']), 0, 0, 'L');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, 'Retirado por:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode($data['retirado_por_nombre']), 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, 'Parentesco:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode($data['retirado_por_parentesco']), 0, 1);
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'Motivo de la Salida:', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode($data['motivo']), 0, 'L');
$pdf->Ln(4);

// Fecha y Hora de salida movidas aquí
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, 'Fecha y Hora:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, date("d/m/Y", strtotime($data['fecha_salida'])) . '  -  ' . date("g:i A", strtotime($data['hora_salida'])), 0, 1);
$pdf->Ln(10);

// --- Firmas 0---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, '___________________________', 0, 1, 'L') . '    ' . $pdf->Cell(0, 0, '___________________________', 0, 1, 'R');
$pdf->Cell(0, 5, utf8_decode('Firma del Representante que Retira'), 0, 1, 'L') . '    ' . $pdf->Cell(0, 3, utf8_decode('Firma del Personal Autorizado (CEIA)'), 0, 1, 'R');
$pdf->Ln(6);

// --- Imagen de pie de página ---
$pageWidth = $pdf->GetPageWidth();
$imageWidth = 100; // Ancho deseado para la imagen
$x = ($pageWidth - $imageWidth) / 2;
$pdf->Image(__DIR__.'/../../public/img/pie_pag.png', $x, $pdf->GetY(), $imageWidth);


// 5. Salida del PDF
$pdf->Output('I', 'Planilla_Autorizacion_' . $data['id'] . '.pdf');
?>