<?php
session_start();
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/lib/fpdf.php';

// 1. Validar ID de autorización de staff
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Error: ID de autorización no válido o no proporcionado.");
}

// 2. Obtener datos de la base de datos de forma explícita
try {
    $periodo_activo_info = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nombre_periodo = $periodo_activo_info ? $periodo_activo_info['nombre_periodo'] : 'No definido';

    // Consulta explícita para asegurar que obtenemos los campos necesarios y intentamos obtener fecha_creacion
    $stmt = $conn->prepare(
        "SELECT 
            a.id,
            a.fecha_permiso,
            a.hora_salida,
            a.duracion_horas,
            a.motivo,
            a.fecha_creacion, -- Se intenta seleccionar, si no existe, será null
            p.nombre_completo,
            p.categoria
         FROM autorizaciones_salida_staff a
         JOIN profesores p ON a.profesor_id = p.id
         WHERE a.id = :id"
    );
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Error: Autorización de staff no encontrada.");
    }
} catch (Exception $e) {
    // Si la consulta falla (ej. porque 'fecha_creacion' no existe), reintentar sin ella
    if (strpos($e->getMessage(), 'fecha_creacion') !== false) {
        try {
            $stmt = $conn->prepare(
                "SELECT a.id, a.fecha_permiso, a.hora_salida, a.duracion_horas, a.motivo, p.nombre_completo, p.categoria
                 FROM autorizaciones_salida_staff a
                 JOIN profesores p ON a.profesor_id = p.id
                 WHERE a.id = :id"
            );
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $data['fecha_creacion'] = null; // Asegurarse de que no dé error más adelante
            if (!$data) {
                die("Error: Autorización de staff no encontrada (reintento).");
            }
        } catch (Exception $e2) {
            die('Error al consultar la base de datos (reintento): ' . $e2->getMessage());
        }
    } else {
        die('Error al consultar la base de datos: ' . $e->getMessage());
    }
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
        $this->Ln(10);
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
$pdf->SetFont('Arial', '', 10);

// --- Contenido de la Planilla ---

// Lógica robusta para la fecha de emisión
$fecha_emision_valida = !empty($data['fecha_creacion']) ? $data['fecha_creacion'] : $data['fecha_permiso'];

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, 'Fecha de Emision:', 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, date("d/m/Y", strtotime($fecha_emision_valida)), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, 'Personal:', 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode($data['nombre_completo'] ?? 'N/A'), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, utf8_decode('Categoría:'), 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode($data['categoria'] ?? 'N/A'), 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(45, 8, 'Fecha de la Salida:', 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, !empty($data['fecha_permiso']) ? date("d/m/Y", strtotime($data['fecha_permiso'])) : 'N/A', 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(45, 8, 'Hora de la Salida:', 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, !empty($data['hora_salida']) ? date("g:i A", strtotime($data['hora_salida'])) : 'N/A', 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(45, 8, utf8_decode('Duración Aprox. (Horas):'), 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, $data['duracion_horas'] ?? 'N/A', 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Motivo de la Salida:', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 6, utf8_decode($data['motivo'] ?? 'Sin motivo especificado.'), 0, 'L');
$pdf->Ln(5);

// --- Firmas ---
$y_position = $pdf->GetY() + 20;
if ($y_position > $pdf->GetPageHeight() - 40) { // Evitar que las firmas se salgan de la página
    $pdf->AddPage();
    $y_position = $pdf->GetY();
}
$pdf->SetY($y_position);
$line_width = 80;

$pdf->Cell($line_width, 7, '___________________________', 0, 0, 'C');
$pdf->SetX($pdf->GetPageWidth() - $line_width - 10);
$pdf->Cell($line_width, 7, '___________________________', 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell($line_width, 6, utf8_decode('Firma del Personal'), 0, 0, 'C');
$pdf->SetX($pdf->GetPageWidth() - $line_width - 10);
$pdf->Cell($line_width, 6, utf8_decode('Personal Autorizado (Dirección)'), 0, 1, 'C');
$pdf->Ln(10);

// --- Imagen de pie de página ---
$pageWidth = $pdf->GetPageWidth();
$imageWidth = 100;
$x = ($pageWidth - $imageWidth) / 2;
$y_pie = $pdf->GetY();
if ($y_pie > $pdf->GetPageHeight() - 30) {
    $y_pie = $pdf->GetPageHeight() - 30;
}
$pdf->Image(__DIR__.'/../../public/img/pie_pag.png', $x, $y_pie, $imageWidth);

// 5. Salida del PDF
$pdf->Output('I', 'Autorizacion_Staff_' . ($data['id'] ?? '0') . '.pdf');
?>