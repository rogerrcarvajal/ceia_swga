<?php
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
?>