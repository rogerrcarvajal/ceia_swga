<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/libs/fpdf.php';

$filtro_apellido = $_GET['apellido'] ?? '';
$filtro_semana = $_GET['semana'] ?? '';
$param_apellido = '%' . strtolower($filtro_apellido) . '%';

$where = "WHERE LOWER(e.apellido_completo) LIKE ?";
$params = [$param_apellido];

if ($filtro_semana !== '') {
    $where .= " AND EXTRACT(WEEK FROM m.fecha_movimiento) = ?";
    $params[] = (int)$filtro_semana;
}

$sql = "
    SELECT m.id, v.placa, v.modelo, e.nombre_completo || ' ' || e.apellido_completo AS estudiante,
           m.fecha_movimiento, m.hora_movimiento
    FROM movimientos_vehiculos m
    JOIN vehiculos v ON m.vehiculo_id = v.id
    JOIN estudiantes e ON v.estudiante_id = e.id
    $where
    ORDER BY m.fecha_movimiento DESC, m.hora_movimiento DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

class PDF extends FPDF {
    function Header() {
        $this->Image(__DIR__ . '/../../public/img/logo_ceia.png',10,6,25);
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Reporte de Entrada/Salida de Vehículos',0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'CEIA - SWGA - '.date('d/m/Y H:i'),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);

$filtros = [];
if ($filtro_apellido !== '') $filtros[] = "Apellido: " . ucfirst($filtro_apellido);
if ($filtro_semana !== '') $filtros[] = "Semana: " . $filtro_semana;

$pdf->SetFont('Arial','',11);
if ($filtros) {
    $pdf->MultiCell(0, 8, 'Filtros aplicados: ' . implode(' | ', $filtros), 0, 'L');
}
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,220,255);
$pdf->Cell(10,8,'#',1,0,'C',true);
$pdf->Cell(30,8,'Placa',1,0,'C',true);
$pdf->Cell(35,8,'Modelo',1,0,'C',true);
$pdf->Cell(60,8,'Estudiante',1,0,'C',true);
$pdf->Cell(30,8,'Fecha',1,0,'C',true);
$pdf->Cell(25,8,'Hora',1,1,'C',true);

$pdf->SetFont('Arial','',10);

if ($data) {
    $i = 1;
    foreach ($data as $row) {
        $pdf->Cell(10,8,$i++,1,0,'C');
        $pdf->Cell(30,8,$row['placa'],1);
        $pdf->Cell(35,8,$row['modelo'],1);
        $pdf->Cell(60,8,utf8_decode($row['estudiante']),1);
        $pdf->Cell(30,8,$row['fecha_movimiento'],1);
        $pdf->Cell(25,8,$row['hora_movimiento'],1,1);
    }
} else {
    $pdf->Cell(0,10,'No se encontraron registros para los filtros aplicados.',1,1,'C');
}

$pdf->Ln(5);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,10, 'Total de movimientos: ' . count($data), 0, 1, 'R');

// Generar nombre del archivo PDF dinámico
$nombre_estudiante = '';
if (!empty($data)) {
    // Tomamos el nombre del primer registro, ya que todos pertenecen al mismo estudiante filtrado
    $nombre_estudiante = $data[0]['estudiante'] ?? 'Estudiante';
    $nombre_estudiante = str_replace(' ', '_', $nombre_estudiante); // Reemplazar espacios por guiones bajos
    $nombre_estudiante = preg_replace('/[^A-Za-z0-9_]/', '', $nombre_estudiante); // Limpiar caracteres especiales
}

$nombre_archivo = "Movimiento_de_Vehiculo_del_Estudiante_" . $nombre_estudiante . ".pdf";
$pdf->Output('I', $nombre_archivo);