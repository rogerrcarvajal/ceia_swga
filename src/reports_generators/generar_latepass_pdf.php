<?php
// Incluir archivos necesarios.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/pdf_base.php';

$semana = filter_var($_GET['semana'] ?? 0, FILTER_VALIDATE_INT);
if (!$semana) {
    die('Semana recibida: ' . htmlspecialchars($_GET['semana'] ?? 'vacía'));
}

$grado = $_GET['grado'] ?? 'todos';

if (!$semana) die('Semana no válida');

// Obtener datos
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$sql = "
    SELECT
        e.nombre_completo, e.apellido_completo, ep.grado_cursado,
        TO_CHAR(lt.fecha_registro, 'MM-DD-YYYY') as fecha_registro,
        lt.hora_llegada,
        CAST((SELECT COUNT(*) FROM llegadas_tarde WHERE estudiante_id = lt.estudiante_id AND semana_del_anio = :semana AND CAST(hora_llegada AS TIME) > '08:05:59') AS INTEGER) as strikes,
        (CASE
            WHEN CAST((SELECT COUNT(*) FROM llegadas_tarde WHERE estudiante_id = lt.estudiante_id AND semana_del_anio = :semana AND CAST(hora_llegada AS TIME) > '08:05:59') AS INTEGER) >= 3
            THEN 'Visite SWGA para mayor informacion'
            ELSE COALESCE(rs.ultimo_mensaje, '')
        END) as ultimo_mensaje
    FROM llegadas_tarde lt
    JOIN estudiantes e ON lt.estudiante_id = e.id
    JOIN estudiante_periodo ep ON e.id = ep.estudiante_id AND ep.periodo_id = :pid
    LEFT JOIN latepass_resumen_semanal rs ON lt.estudiante_id = rs.estudiante_id AND rs.semana_del_anio = lt.semana_del_anio AND rs.periodo_id = ep.periodo_id
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
        $estudiante_text = utf8_decode($a['nombre_completo'] . ' ' . $a['apellido_completo']);
        $fecha_text = $a['fecha_registro'];
        $hora_text = $a['hora_llegada'];
        $strikes_text = $a['strikes'];
        $observacion_text = utf8_decode(str_replace('<br>', "\n", $a['ultimo_mensaje'] ?? ''));

        // Store current X and Y
        $x_start = $pdf->GetX();
        $y_start = $pdf->GetY();

        // Calculate height for MultiCell
        $nb = $pdf->NbLines(50, $observacion_text); // 50 is the width of the cell
        $row_height = $nb * 6; // 6 is the line height

        // Draw Estudiante
        $pdf->SetXY($x_start, $y_start);
        $pdf->MultiCell(60, 6, $estudiante_text, 1, 'L', 0); // Use 6 as line height, not row_height

        // Draw Fecha
        $pdf->SetXY($x_start + 60, $y_start);
        $pdf->MultiCell(30, 6, $fecha_text, 1, 'L', 0);

        // Draw Hora
        $pdf->SetXY($x_start + 60 + 30, $y_start);
        $pdf->MultiCell(25, 6, $hora_text, 1, 'L', 0);

        // Draw Strikes
        $pdf->SetXY($x_start + 60 + 30 + 25, $y_start);
        $pdf->MultiCell(20, 6, $strikes_text, 1, 'C', 0);

        // Draw Observación
        $pdf->SetXY($x_start + 60 + 30 + 25 + 20, $y_start);
        $pdf->MultiCell(50, 6, $observacion_text, 1, 'L', 0);

        // Move to the next line, considering the maximum height of the current row
        $pdf->SetY($y_start + $row_height);
        $pdf->SetX(10); // Reset X to the left margin (assuming 10mm left margin)
    }
    $pdf->Ln(5);
}

// Nombre del archivo de salida
$pdf->Output('D', "LatePass_Semana_$semana" . ($grado !== 'todos' ? "_$grado" : '') . ".pdf");

?>
}

// Nombre del archivo de salida
$pdf->Output('D', "LatePass_Semana_$semana" . ($grado !== 'todos' ? "_$grado" : '') . ".pdf");

?>