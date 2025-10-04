<?php
// api/consultar_salidas.php

header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['status' => 'error', 'mensaje' => 'Petición inválida'];

if (isset($_GET['semana']) && !empty($_GET['semana'])) {
    $semana = $_GET['semana'];
    // Formato esperado: YYYY-Www, por ejemplo: 2025-W41
    $parts = explode('-W', $semana);
    
    if (count($parts) === 2) {
        $year = (int)$parts[0];
        $week = (int)$parts[1];

        try {
            $date = new DateTime();
            $date->setISODate($year, $week);
            $fecha_inicio = $date->format('Y-m-d');
            $date->modify('+6 days');
            $fecha_fin = $date->format('Y-m-d');

            $stmt = $conn->prepare(
                "SELECT 
                    to_char(a.fecha_salida, 'DD/MM/YYYY') as fecha_salida,
                    to_char(a.hora_salida, 'HH12:MI AM') as hora_salida,
                    e.nombre_completo || ' ' || e.apellido_completo as nombre_estudiante,
                    a.retirado_por_nombre,
                    a.retirado_por_parentesco,
                    a.motivo
                 FROM autorizaciones_salida a
                 JOIN estudiantes e ON a.estudiante_id = e.id
                 WHERE a.fecha_salida BETWEEN :fecha_inicio AND :fecha_fin
                 ORDER BY a.fecha_salida, a.hora_salida"
            );
            $stmt->execute([
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ]);
            
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'exito',
                'registros' => $registros
            ];

        } catch (Exception $e) {
            $response['mensaje'] = 'Error al procesar la fecha: ' . $e->getMessage();
        }
    } else {
        $response['mensaje'] = 'Formato de semana no válido. Se esperaba YYYY-Www.';
    }
} else {
    $response['mensaje'] = 'No se proporcionó el parámetro de semana.';
}

echo json_encode($response);
?>
