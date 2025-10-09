<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// 1. Validar ID de autorización
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Error: ID de autorización no válido.");
}

// 2. Obtener datos de la base de datos
try {
    $periodo_activo_info = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nombre_periodo = $periodo_activo_info ? $periodo_activo_info['nombre_periodo'] : 'No definido';

    $stmt = $conn->prepare(
        "SELECT a.*, p.nombre_completo, p.cedula, pp.posicion
         FROM autorizaciones_salida_staff a
         JOIN profesores p ON a.profesor_id = p.id
         LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id AND pp.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1)
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
        $this->Ln(5);
        $this->Image(__DIR__.'/../../public/img/logo_ceia.png', 10, 13, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 8, utf8_decode('Planilla de Autorización de Salida de Estudiantes'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode('Período Escolar: ' . $this->periodo_activo), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode('SOLICITUD DE PERMISO'), 0, 1, 'C');
        $this->Ln(12);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// 4. Crear instancia de PDF y generar contenido
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// --- Contenido de la Planilla ---

// Fecha de Solicitud
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 7, utf8_decode('Fecha de Solicitud:'));
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, date("d/m/Y", strtotime($data['fecha_permiso'])), 0, 1);

// Nombre y Apellido
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 7, utf8_decode('Nombre y Apellido:'));
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, utf8_decode($data['nombre_completo']), 0, 1);

// Cédula y Posición
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 7, utf8_decode('Cédula:'));
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(55, 7, $data['cedula'], 0, 0);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(20, 7, utf8_decode('Posición:'));
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, utf8_decode($data['posicion'] ?? 'No asignada'), 0, 1);
$pdf->Ln(10);

// --- Detalles del Permiso ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Detalles del Permiso'), 0, 1, 'C');
$pdf->Line(10, $pdf->GetY(), 205, $pdf->GetY());
$pdf->Ln(5);

// Fecha y Hora del Permiso
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 7, 'Fecha del Permiso:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(55, 7, date("d/m/Y", strtotime($data['fecha_permiso'])), 0, 0);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 7, 'Hora de Salida:', 0, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, date("g:i A", strtotime($data['hora_salida'])), 0, 1);

// Duración
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 7, utf8_decode('Duración (Horas):'));
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, $data['duracion_horas'], 0, 1);

// Motivo
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 7, 'Motivo:', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($data['motivo']), 0, 'L');
$pdf->Ln(25);

// --- Firmas ---
$y_firmas = $pdf->GetY();

// Supervisor Inmediato
$pdf->SetXY(20, $y_firmas);
$pdf->Cell(60, 5, '__________________________', 0, 1, 'C');
$pdf->SetX(20);
$pdf->Cell(60, 5, 'Supervisor Inmediato', 0, 1, 'C');

// Administración
$pdf->SetXY(130, $y_firmas);
$pdf->Cell(60, 5, '__________________________', 0, 1, 'C');
$pdf->SetX(130);
$pdf->Cell(60, 5, utf8_decode('Administración'), 0, 1, 'C');

$pdf->Ln(15);

// Personal
$y_personal = $pdf->GetY();
$pdf->SetX(75); // Centrar la firma del personal
$pdf->Cell(60, 5, '__________________________', 0, 1, 'C');
$pdf->SetX(75);
$pdf->Cell(60, 5, 'Firma del Personal', 0, 1, 'C');
$pdf->Ln(70);

// --- Imagen de pie de página ---
$pageWidth = $pdf->GetPageWidth();
$imageWidth = 100; // Ancho deseado para la imagen
$x = ($pageWidth - $imageWidth) / 2;
$pdf->Image(__DIR__.'/../../public/img/pie_pag.png', $x, $pdf->GetY(), $imageWidth);

// 5. Salida del PDF
$pdf->Output('I', 'Solicitud_Permiso_' . $data['id'] . '.pdf');
?>
