<?php
<<<<<<< HEAD
require "conn/conexion.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=LatePass_CEIA_" . date('Ymd_His') . ".xls");

$grado = $_GET['grado'] ?? '';
$fecha_inicio = $_GET['inicio'] ?? '';
$fecha_fin = $_GET['fin'] ?? '';

if (empty($grado) || empty($fecha_inicio) || empty($fecha_fin)) {
    die("Faltan parámetros para la exportación.");
}

$sql = "SELECT e.nombre_completo, e.grado_ingreso, l.hora_llegada, TO_CHAR(l.fecha_registro, 'DD/MM/YYYY') as fecha
        FROM llegadas_tarde l 
        JOIN estudiantes e ON l.estudiante_id = e.id 
        WHERE e.grado_ingreso = :grado AND l.fecha_registro BETWEEN :inicio AND :fin
        ORDER BY l.fecha_registro ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':grado' => $grado,
    ':inicio' => $fecha_inicio,
    ':fin' => $fecha_fin
]);

$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Nombre\tGrado\tFecha\tHora Llegada\n";

foreach ($datos as $dato) {
    echo "{$dato['nombre_completo']}\t{$dato['grado_ingreso']}\t{$dato['fecha']}\t{$dato['hora_llegada']}\n";
}
=======
// Carga el autoloader de Composer
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// --- CONEXIÓN Y CONSULTA A LA BD (igual que en los otros archivos) ---
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_basedatos";
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT *, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM jugadores WHERE status = 'activo' ORDER BY apellidos, nombres";
$result = $conn->query($sql);

// --- CREACIÓN DEL EXCEL ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Roster Actual');

// Encabezados de la tabla
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Nombre Completo');
$sheet->setCellValue('C1', 'Posición');
$sheet->setCellValue('D1', 'B/L');
$sheet->setCellValue('E1', 'Estatura (cm)');
$sheet->setCellValue('F1', 'Peso (kg)');
$sheet->setCellValue('G1', 'Fecha Nacimiento');
$sheet->setCellValue('H1', 'Edad');
$sheet->setCellValue('I1', 'Lugar de Nacimiento');

// Estilo para los encabezados
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '003366']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

// Llenar datos de jugadores
$rowNum = 2;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNum, $row['numero']);
        $sheet->setCellValue('B' . $rowNum, $row['apellidos'] . ", " . $row['nombres']);
        $sheet->setCellValue('C' . $rowNum, $row['posicion']);
        $sheet->setCellValue('D' . $rowNum, $row['batea']."/".$row['lanza']);
        $sheet->setCellValue('E' . $rowNum, $row['estatura_cm']);
        $sheet->setCellValue('F' . $rowNum, $row['peso_kg']);
        $sheet->setCellValue('G' . $rowNum, date("d/m/Y", strtotime($row["fecha_nacimiento"])));
        $sheet->setCellValue('H' . $rowNum, $row['edad']);
        $sheet->setCellValue('I' . $rowNum, $row['lugar_nacimiento']);
        $rowNum++;
    }
}
$conn->close();

// Auto-ajustar ancho de columnas
foreach(range('A','I') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// --- ENVIAR EL ARCHIVO AL NAVEGADOR ---
$writer = new Xlsx($spreadsheet);
$filename = 'Roster_Actual_2024-2025.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
>>>>>>> 70493ff (Actualizacion de archivos del sistema)
?>