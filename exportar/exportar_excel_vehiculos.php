<?php
require "conn/conexion.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Control_Vehiculos_" . date('Ymd_His') . ".xls");

$sql = "SELECT e.nombre_completo, v.placa, v.conductor_nombre, TO_CHAR(v.fecha_hora, 'DD/MM/YYYY HH24:MI') as fecha_hora
        FROM vehiculos_autorizados v
        JOIN estudiantes e ON v.estudiante_id = e.id
        ORDER BY v.fecha_hora DESC";

$stmt = $conn->query($sql);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Estudiante\tPlaca\tConductor\tFecha y Hora\n";

foreach ($datos as $dato) {
    echo "{$dato['nombre_completo']}\t{$dato['placa']}\t{$dato['conductor_nombre']}\t{$dato['fecha_hora']}\n";
}
?>