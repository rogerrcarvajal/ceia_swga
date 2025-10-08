<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/fpdf.php';

class PDF extends FPDF
{
    private $periodo_activo;

    function setPeriodoActivo($periodo) {
        $this->periodo_activo = $periodo;
    }

    function Header()
    {
        $this->Image(__DIR__.'/../../public/img/logo_ceia.png', 10, 8, 20);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('Reporte de Salidas de Estudiantes'), 0, 0, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 15, 'Fecha: ' . date('d/m/Y'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        $this->Ln(10);
        if ($this->periodo_activo) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, utf8_decode('Período Escolar: ' . $this->periodo_activo), 0, 1, 'C');
        }
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function FancyTable($header, $data)
    {
        $this->SetFillColor(200, 220, 255);
        $this->SetTextColor(0);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Anchos de las columnas
        $w = array(25, 22, 60, 45, 25, 100);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetFont('', '');
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row['fecha_salida'], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row['hora_salida'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, utf8_decode($row['nombre_estudiante']), 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, utf8_decode($row['retirado_por_nombre']), 'LR', 0, 'L', $fill);
            $this->Cell($w[4], 6, utf8_decode($row['retirado_por_parentesco']), 'LR', 0, 'L', $fill);
            $this->MultiCell($w[5], 6, utf8_decode($row['motivo']), 'LR', 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// --- Lógica Principal ---
if (!isset($_GET['semana']) || empty($_GET['semana'])) {
    die('No se proporcionó el parámetro de semana.');
}

$semana = $_GET['semana'];
$estudiante_id = $_GET['estudiante_id'] ?? 'todos';

$parts = explode('-W', $semana);
if (count($parts) !== 2) {
    die('Formato de semana no válido. Se esperaba YYYY-Www.');
}

$year = (int)$parts[0];
$week = (int)$parts[1];

try {
    $date = new DateTime();
    $date->setISODate($year, $week);
    $fecha_inicio = $date->format('Y-m-d');
    $date->modify('+6 days');
    $fecha_fin = $date->format('Y-m-d');

    // Obtener período activo
    $periodo_activo_info = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nombre_periodo = $periodo_activo_info ? $periodo_activo_info['nombre_periodo'] : 'No definido';

    $sql = "SELECT 
                to_char(a.fecha_salida, 'DD/MM/YYYY') as fecha_salida,
                to_char(a.hora_salida, 'HH12:MI AM') as hora_salida,
                e.nombre_completo || ' ' || e.apellido_completo as nombre_estudiante,
                a.retirado_por_nombre,
                a.retirado_por_parentesco,
                a.motivo
            FROM autorizaciones_salida a
            JOIN estudiantes e ON a.estudiante_id = e.id
            WHERE a.fecha_salida BETWEEN :fecha_inicio AND :fecha_fin";

    $params = [
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
    ];

    if ($estudiante_id !== 'todos' && !empty($estudiante_id)) {
        $sql .= " AND a.estudiante_id = :estudiante_id";
        $params[':estudiante_id'] = $estudiante_id;
    }

    $sql .= " ORDER BY a.fecha_salida, a.hora_salida";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Crear PDF
    $pdf = new PDF('L', 'mm', 'Letter');
    $pdf->setPeriodoActivo($nombre_periodo);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    if (empty($registros)) {
        $pdf->Cell(0, 10, 'No se encontraron registros para la semana seleccionada.', 0, 1, 'C');
    } else {
        $header = ['Fecha', 'Hora', 'Estudiante', 'Retirado por', 'Parentesco', 'Motivo'];
        $pdf->FancyTable($header, $registros);
    }

    $pdf->Output('I', "Reporte_Salidas_Semana_{$week}_{$year}.pdf");

} catch (Exception $e) {
    die('Error al generar el reporte: ' . $e->getMessage());
}
?>