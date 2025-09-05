<?php
require_once __DIR__ . '/../config.php';
<<<<<<< HEAD
require_once __DIR__ . '/pdf_base.php';
=======
require_once __DIR__ . '/../lib/fpdf/fpdf.php';
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec

date_default_timezone_set('America/Caracas');

$semana = $_GET['semana'] ?? '';
$staff_id = $_GET['staff_id'] ?? 0;

// Obtener datos
$params = [];
$where = [];

if ($semana) {
    $inicio_semana = date('Y-m-d', strtotime($semana));
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));
    $where[] = "es.fecha BETWEEN :start AND :end";
    $params[':start'] = $inicio_semana;
    $params[':end'] = $fin_semana;
}

if ($staff_id > 0) {
    $where[] = "es.profesor_id = :staff_id";
    $params[':staff_id'] = $staff_id;
}

$sql = "
<<<<<<< HEAD
    SELECT p.nombre_completo, es.fecha, es.hora_entrada, es.hora_salida, es.ausente
=======
    SELECT p.nombre_completo, TO_CHAR(es.fecha, 'YYYY-MM-DD') as fecha, es.hora_entrada, es.hora_salida, es.ausente
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec
    FROM entrada_salida_staff es
    JOIN profesores p ON es.profesor_id = p.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

<<<<<<< HEAD
$sql .= " ORDER BY es.fecha DESC";
=======
$sql .= " ORDER BY p.nombre_completo, es.fecha DESC";
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

<<<<<<< HEAD
// Nombre del profesor para título
$nombre_profesor = $datos[0]['nombre_completo'] ?? 'Personal';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nombre_del_periodo = $periodo_activo['nombre_periodo'] ?? 'No Definido';

$pdf = new LatePassPDF('P', 'mm', 'A4', $nombre_del_periodo);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Reporte de Movimiento del Staff"), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode($nombre_profesor), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 8, 'Fecha', 1);
$pdf->Cell(35, 8, 'Hora Entrada', 1);
$pdf->Cell(35, 8, 'Hora Salida', 1);
$pdf->Cell(30, 8, 'Ausente', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

foreach ($datos as $row) {
    $pdf->Cell(40, 8, date("m-d-Y", strtotime($row['fecha'])), 1);
    $pdf->Cell(35, 8, $row['hora_entrada'] ?? '-', 1);
    $pdf->Cell(35, 8, $row['hora_salida'] ?? '-', 1);
    $pdf->Cell(30, 8, $row['ausente'] ? 'Sí' : 'No', 1);
    $pdf->Ln();
}

$pdf->Output('D', "Movimiento del Staff {$nombre_profesor}.pdf");
=======
// Definir el título del PDF
$titulo = "Reporte de Movimiento del Staff";
if ($staff_id > 0 && !empty($datos)) {
    $titulo .= ": " . $datos[0]['nombre_completo'];
} elseif ($semana) {
    $titulo .= " - Semana del " . date('d/m/Y', strtotime($semana));
} else {
    $titulo .= " (General)";
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, $titulo, 0, 1, 'C');
$pdf->Ln(5);

if (empty($datos)) {
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, "No se encontraron registros para los filtros seleccionados.", 0, 1, 'C');
} else {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 8, 'Nombre Completo', 1);
    $pdf->Cell(30, 8, 'Fecha', 1);
    $pdf->Cell(30, 8, 'Hora Entrada', 1);
    $pdf->Cell(30, 8, 'Hora Salida', 1);
    $pdf->Cell(20, 8, 'Ausente', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);
    foreach ($datos as $row) {
        // Para evitar problemas de codificación, se intenta convertir a ISO-8859-1 si es necesario
        $nombre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $row['nombre_completo']);

        $pdf->Cell(60, 8, $nombre, 1);
        $pdf->Cell(30, 8, $row['fecha'], 1);
        $pdf->Cell(30, 8, $row['hora_entrada'] ?? '-', 1);
        $pdf->Cell(30, 8, $row['hora_salida'] ?? '-', 1);
        $pdf->Cell(20, 8, $row['ausente'] ? 'Si' : 'No', 1);
        $pdf->Ln();
    }
}

$pdf->Output('I', "Movimiento_Staff.pdf");
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec
