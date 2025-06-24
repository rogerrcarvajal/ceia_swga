<?php
require "conn/conexion.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Control_Profesores_" . date('Ymd_His') . ".xls");

$sql = "SELECT p.nombre_completo, m.tipo_movimiento, TO_CHAR(m.fecha_hora, 'DD/MM/YYYY HH24:MI') as fecha_hora
        FROM entrada_salida_profesores m
        JOIN profesores p ON m.profesor_id = p.id
        ORDER BY m.fecha_hora DESC";

$stmt = $conn->query($sql);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Profesor\tMovimiento\tFecha y Hora\n";

foreach ($datos as $dato) {
    echo "{$dato['nombre_completo']}\t{$dato['tipo_movimiento']}\t{$dato['fecha_hora']}\n";
}
?>