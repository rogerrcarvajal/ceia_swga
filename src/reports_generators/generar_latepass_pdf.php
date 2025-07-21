<?php
// Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

$semana = filter_var($_GET['semana'] ?? 0, FILTER_VALIDATE_INT);
if (!$semana) {
    die('Semana recibida: ' . htmlspecialchars($_GET['semana'] ?? 'vacía'));
}

$grado = $_GET['grado'] ?? 'todos';

if (!$semana) die('Semana no válida');

// Obtener datos
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$sql = "
    SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado, lt.fecha_registro, lt.hora_llegada, COALESCE(rs.conteo_tardes,0) AS strikes, rs.ultimo_mensaje
    FROM llegadas_tarde lt
    JOIN estudiantes e ON lt.estudiante_id = e.id
    JOIN estudiante_periodo ep ON e.id = ep.estudiante_id AND ep.periodo_id = :pid
    LEFT JOIN latepass_resumen_semanal rs ON lt.estudiante_id = rs.estudiante_id AND rs.semana_del_anio = lt.semana_del_anio
    WHERE lt.semana_del_anio = :semana AND ep.periodo_id = :pid";
$params = [':semana' => $semana, ':pid' => $periodo_activo['id']];

if ($grado !== 'todos' && !empty($grado)) {
            $sql .= " AND ep.grado_cursado = :grado";
            $params[':grado'] = $grado;
        }

        $sql .= " ORDER BY lt.fecha_registro DESC, lt.hora_llegada DESC";

$nombre_del_periodo = $periodo_activo['nombre_periodo'] ?? 'No Definido';


$stmt = $conn->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por grado
$datos = [];
foreach ($registros as $r) {
    $datos[$r['grado_cursado']][] = $r;
}

// --- CLASE PDF PERSONALIZADA ---
class LatePassPDF extends FPDF
{
    private $nombre_periodo;

    function __construct($orientation, $unit, $size, $periodo_nombre) {
        parent::__construct($orientation, $unit, $size);
        $this->nombre_periodo = $periodo_nombre;
    }

    function Header() {
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/logo_ceia.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Centro Educativo Internacional Anzoategui', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 100, 0); // Verde oscuro
        $this->Cell(0, 5, 'Periodo Escolar Activo: ' . $this->nombre_periodo, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0); // Restaurar a negro
        $this->Ln(10);
    }

    function Footer() {
        // Posición a 2 cm del final
        $this->SetY(-20);
        // Imagen de línea de colores
        $this->Image($_SERVER['DOCUMENT_ROOT'] . '/public/img/color_line.png', 10, $this->GetY(), 190);
        
        $this->SetY(-15); // Posición a 1.5 cm del final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('Av. José Antonio Anzoátegui, Km 98 - Anaco, Edo Anzoátegui 6003, Venezuela - +58 282 422 2683'), 0, 1, 'C');
        $this->Cell(0, 5, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 8, $title, 1, 1, 'L', true);
        $this->Ln(2);
    }

    function DataRow($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 7, utf8_decode($label), 'B', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 7, utf8_decode($value ?? 'N/A'), 'B', 1);
    }
}

// --- GENERACIÓN DEL DOCUMENTO PDF ---
$pdf = new LatePassPDF('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Late-Pass de la ' . 'Semana ' . $semana), 0, 1, 'C');
$pdf->Ln(5);

foreach ($datos as $grado => $alumnos) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, utf8_decode("Grado: $grado"), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(60, 6, utf8_decode('Estudiante'), 1);
    $pdf->Cell(30, 6, utf8_decode('Fecha'), 1);
    $pdf->Cell(25, 6, utf8_decode('Hora'), 1);
    $pdf->Cell(20, 6, utf8_decode('Strikes'), 1);
    $pdf->Cell(50, 6, utf8_decode('Observación'), 1);
    $pdf->Ln();

    foreach ($alumnos as $a) {
        $pdf->Cell(60, 6, utf8_decode($a['nombre_completo'] . ' ' . $a['apellido_completo']), 1);
        $pdf->Cell(30, 6, $a['fecha_registro'], 1);
        $pdf->Cell(25, 6, $a['hora_llegada'], 1);
        $pdf->Cell(20, 6, $a['strikes'], 1);
        $pdf->Cell(50, 6, utf8_decode($a['ultimo_mensaje'] ?? ''), 1);
        $pdf->Ln();
    }
    $pdf->Ln(5);
}

// Nombre del archivo de salida
$nombre_archivo = "LatePass_Semana_$semana" . ($grado !== 'todos' ? "_$grado" : '' . ".pdf");
$pdf->Output('I', $nombre_archivo);

?>