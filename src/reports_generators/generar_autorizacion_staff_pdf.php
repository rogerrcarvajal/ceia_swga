<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

// 1. Validar ID de autorización
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    // Si el ID no viene por GET, intentamos obtenerlo de una variable de sesión o de otro modo.
    // Esto es para que el script pueda ser incluido desde reimprimir_autorizacion_staff.php
    if (isset($autorizacion_id)) {
        $id = $autorizacion_id;
    } else {
        die("Error: ID de autorización no válido o no proporcionado.");
    }
}


// 2. Obtener datos de la base de datos
try {
    $periodo_activo_info = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nombre_periodo = $periodo_activo_info ? $periodo_activo_info['nombre_periodo'] : 'No definido';

    $stmt = $conn->prepare(
        "SELECT a.*, p.nombre_completo, p.cedula, p.categoria, p.posicion, u.nombre_usuario as registrado_por
         FROM autorizaciones_salida_staff a
         JOIN profesores p ON a.profesor_id = p.id
         JOIN usuarios u ON a.registrado_por_usuario_id = u.id
         WHERE a.id = :id"
    );
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Error: Autorización de staff no encontrada.");
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
        $this->Image(__DIR__.'/../../public/img/logo_ceia.png', 10, 12, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 8, utf8_decode('Planilla de Autorización de Salida de Personal'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode('Período Escolar: ' . $this->periodo_activo), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 6);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// 4. Crear instancia de PDF y generar contenido
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->setPeriodoActivo($nombre_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// --- Contenido de la Planilla ---

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, 'Nombre del Personal:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 7, utf8_decode($data['nombre_completo']), 0, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Cédula:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, utf8_decode($data['cedula']), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Categoría:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, utf8_decode($data['categoria']), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Posición / Cargo:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, utf8_decode($data['posicion']), 0, 1);
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, 'Fecha del Permiso:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, date("d/m/Y", strtotime($data['fecha_permiso'])), 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, 'Hora de Salida:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, date("h:i A", strtotime($data['hora_salida'])), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Duración (Horas):'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, $data['duracion_horas'], 0, 1);
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'Motivo del Permiso:', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode($data['motivo']), 0, 'L');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, 'Registrado Por:', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, utf8_decode($data['registrado_por']), 0, 1);
$pdf->Ln(10);


// --- Firmas ---
$pdf->SetFont('Arial', '', 10);

$line_width = 80; 
$y_position = $pdf->GetY();
$pdf->Cell($line_width, 7, '___________________________', 0, 0, 'L');
$pdf->SetX($pdf->GetPageWidth() - $line_width - 10); 
$pdf->Cell($line_width, 7, '___________________________', 0, 1, 'R');

$pdf->SetFont('Arial', '', 8);
$pdf->Cell($line_width, 5, utf8_decode('Firma del Personal'), 0, 0, 'L');
$pdf->SetX($pdf->GetPageWidth() - $line_width - 10);
$pdf->Cell($line_width, 5, utf8_decode('Firma del Personal Autorizado (CEIA)'), 0, 1, 'R');
$pdf->Ln(6);

// --- Imagen de pie de página ---
$pageWidth = $pdf->GetPageWidth();
$imageWidth = 100; 
$x = ($pageWidth - $imageWidth) / 2;
$pdf->Image(__DIR__.'/../../public/img/pie_pag.png', $x, $pdf->GetY(), $imageWidth);


// 5. Salida del PDF
$pdf->Output('I', 'Planilla_Autorizacion_Staff_' . $data['id'] . '.pdf');
?>