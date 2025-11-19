<?php
// Activación de reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- VERIFICACIÓN DE ARCHIVOS CRÍTICOS ---
$fpdf_path = __DIR__ . '/../lib/fpdf.php';
$logo_path = __DIR__.'/../../public/img/logo_ceia.png';
$footer_path = __DIR__.'/../../public/img/pie_pag.png';

if (!file_exists($fpdf_path)) {
    die("Error Crítico: No se encuentra el archivo de la librería FPDF en la ruta esperada: " . $fpdf_path);
}
if (!file_exists($logo_path)) {
    die("Error Crítico: No se encuentra la imagen del logo en la ruta esperada: " . $logo_path);
}
if (!file_exists($footer_path)) {
    die("Error Crítico: No se encuentra la imagen del pie de página en la ruta esperada: " . $footer_path);
}

session_start();
require_once __DIR__ . '/../config.php';
require_once $fpdf_path;

// --- INICIO DE LA LÓGICA PRINCIPAL ---
try {
    // 1. Validar ID de autorización
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        die("Error de Parámetro: ID de autorización no válido o no proporcionado.");
    }

    // 2. Obtener datos de la base de datos
    $periodo_activo_info = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nombre_periodo = $periodo_activo_info ? $periodo_activo_info['nombre_periodo'] : 'No definido';
    $periodo_activo_id = $periodo_activo_info ? $periodo_activo_info['id'] : null;

    if (!$periodo_activo_id) {
        // No es un error fatal, pero puede afectar la consulta. Se maneja más adelante.
    }

    $stmt = $conn->prepare(
        "SELECT a.*, p.nombre_completo, p.cedula, pp.posicion
         FROM autorizaciones_salida_staff a
         JOIN profesores p ON a.profesor_id = p.id
         LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id AND pp.periodo_id = :periodo_id
         WHERE a.id = :id"
    );
    $stmt->execute([':id' => $id, ':periodo_id' => $periodo_activo_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Error de Datos: Autorización con ID " . htmlspecialchars($id) . " no fue encontrada en la base de datos.");
    }

    // 3. Definir clase PDF con encabezado y pie de página estándar
    class PDF extends FPDF
    {
        private $periodo_activo;
        private $logo_path;
        
        function setContext($periodo, $logo) {
            $this->periodo_activo = $periodo;
            $this->logo_path = $logo;
        }

        function Header()
        {
            $this->Ln(5);
            $this->Image($this->logo_path, 10, 13, 25);
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 8, utf8_decode('Planilla de Autorización de Salida de Personal/Staff'), 0, 1, 'C');
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
    $pdf->setContext($nombre_periodo, $logo_path);
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 11);

    // --- Contenido de la Planilla (con verificaciones) ---
    $fecha_solicitud = !empty($data['fecha_permiso']) ? date("d/m/Y", strtotime($data['fecha_permiso'])) : 'No especificada';
    $nombre_completo = !empty($data['nombre_completo']) ? utf8_decode($data['nombre_completo']) : 'No especificado';
    $cedula = !empty($data['cedula']) ? $data['cedula'] : 'N/A';
    $posicion = !empty($data['posicion']) ? utf8_decode($data['posicion']) : 'No asignada';
    $hora_salida = !empty($data['hora_salida']) ? date("g:i A", strtotime($data['hora_salida'])) : 'No especificada';
    $duracion = !empty($data['duracion_horas']) ? $data['duracion_horas'] : 'N/A';
    $motivo = !empty($data['motivo']) ? utf8_decode($data['motivo']) : 'Sin motivo.';

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 7, utf8_decode('Fecha de Solicitud:'));
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, $fecha_solicitud, 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 7, utf8_decode('Nombre y Apellido:'));
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, $nombre_completo, 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 7, utf8_decode('Cédula:'));
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(55, 7, $cedula, 0, 0);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20, 7, utf8_decode('Posición:'));
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, $posicion, 0, 1);
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Detalles del Permiso'), 0, 1, 'C');
    $pdf->Line(10, $pdf->GetY(), 205, $pdf->GetY());
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 7, 'Fecha del Permiso:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(55, 7, $fecha_solicitud, 0, 0);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 7, 'Hora de Salida:', 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, $hora_salida, 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 7, utf8_decode('Duración (Horas):'));
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, $duracion, 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 7, 'Motivo:', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 5, $motivo, 0, 'L');
    $pdf->Ln(25);

    $y_firmas = $pdf->GetY();
    $pdf->SetXY(20, $y_firmas);
    $pdf->Cell(60, 5, '__________________________', 0, 1, 'C');
    $pdf->SetX(20);
    $pdf->Cell(60, 5, 'Supervisor Inmediato', 0, 1, 'C');

    $pdf->SetXY(130, $y_firmas);
    $pdf->Cell(60, 5, '__________________________', 0, 1, 'C');
    $pdf->SetX(130);
    $pdf->Cell(60, 5, utf8_decode('Administración'), 0, 1, 'C');
    $pdf->Ln(15);

    $y_personal = $pdf->GetY();
    $pdf->SetX(75);
    $pdf->Cell(60, 5, '__________________________', 0, 1, 'C');
    $pdf->SetX(75);
    $pdf->Cell(60, 5, 'Firma del Personal', 0, 1, 'C');
    $pdf->Ln(70);

    $pageWidth = $pdf->GetPageWidth();
    $imageWidth = 100;
    $x = ($pageWidth - $imageWidth) / 2;
    $pdf->Image($footer_path, $x, $pdf->GetY(), $imageWidth);

    // 5. Salida del PDF
    $pdf->Output('I', 'Solicitud_Permiso_' . $data['id'] . '.pdf');

} catch (Exception $e) {
    die('Error Inesperado: Se produjo una excepción durante la generación del PDF. Mensaje: ' . $e->getMessage());
}
?>