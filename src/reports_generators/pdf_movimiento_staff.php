<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

if (!isset($_GET['semana']) || !isset($_GET['staff_id'])) {
    die("Parámetros inválidos.");
}

$semana = $_GET['semana']; // formato: 2025-W32
$staff_id = $_GET['staff_id'];

// Validar y convertir semana a fechas
try {
    $dt = new DateTime();
    $dt->setISODate(substr($semana, 0, 4), substr($semana, 6));
    $fecha_inicio = $dt->format('Y-m-d');
    $dt->modify('+6 days');
    $fecha_fin = $dt->format('Y-m-d');
} catch (Exception $e) {
    die("Semana inválida.");
}

// Obtener info del staff
$stmt_info = $conn->prepare("
    SELECT p.nombre_completo
    FROM profesores p
    WHERE p.id = :id
");
$stmt_info->execute([':id' => $staff_id]);
$profesor = $stmt_info->fetch(PDO::FETCH_ASSOC);
if (!$profesor) die("Staff no encontrado.");

$nombre_staff = $profesor['nombre_completo'];

// Consultar movimientos
$stmt = $conn->prepare("
    SELECT fecha_movimiento, hora_movimiento, tipo_movimiento, ausente
    FROM movimientos_staff
    WHERE profesor_id = :id
    AND fecha_movimiento BETWEEN :inicio AND :fin
    ORDER BY fecha_movimiento, hora_movimiento
");
$stmt->execute([
    ':id' => $staff_id,
    ':inicio' => $fecha_inicio,
    ':fin' => $fecha_fin
]);

$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar por fecha
$movs_por_fecha = [];
foreach ($registros as $r) {
    $f = $r['fecha_movimiento'];
    if (!isset($movs_por_fecha[$f])) {
        $movs_por_fecha[$f] = ['entrada' => null, 'salida' => null, 'ausente' => $r['ausente']];
    }
    if ($r['tipo_movimiento'] === 'entrada') {
        $movs_por_fecha[$f]['entrada'] = $r['hora_movimiento'];
    } elseif ($r['tipo_movimiento'] === 'salida') {
        $movs_por_fecha[$f]['salida'] = $r['hora_movimiento'];
    }
}

// Generar PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema SWGA');
$pdf->SetTitle("Movimiento Staff - $nombre_staff");
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Reporte de Movimiento de Staff", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln(4);
$pdf->Cell(0, 8, "Nombre del Staff: $nombre_staff", 0, 1);
$pdf->Cell(0, 8, "Semana: $semana ($fecha_inicio a $fecha_fin)", 0, 1);
$pdf->Ln(6);

$html = '
<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #333; padding: 6px; text-align: center; font-size: 11px; }
    th { background-color: #eee; }
</style>
<table>
    <tr>
        <th>Fecha</th>
        <th>Hora Entrada</th>
        <th>Hora Salida</th>
        <th>Ausente</th>
    </tr>';

if (!empty($movs_por_fecha)) {
    foreach ($movs_por_fecha as $fecha => $datos) {
        $html .= "<tr>
            <td>$fecha</td>
            <td>" . ($datos['entrada'] ?? '-') . "</td>
            <td>" . ($datos['salida'] ?? '-') . "</td>
            <td>" . ($datos['ausente'] ? '✔️' : 'No') . "</td>
        </tr>";
    }
} else {
    $html .= '<tr><td colspan="4">No se encontraron registros.</td></tr>';
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Movimiento de Staff - $nombre_staff.pdf", 'I');