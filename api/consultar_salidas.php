<?php
// api/consultar_salidas.php
session_start();
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('America/Caracas');

// Validar rol de usuario
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'master', 'consulta'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Acceso denegado.']);
    exit();
}

$response = ['status' => 'error', 'mensaje' => 'Petición inválida'];

if (isset($_GET['semana']) && !empty($_GET['semana'])) {
    $semana = $_GET['semana'];
    $estudiante_id = $_GET['estudiante_id'] ?? 'todos';

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