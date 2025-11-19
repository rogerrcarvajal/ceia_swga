<?php
session_start();
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// --- CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Acceso denegado.']);
    exit();
}

$response = ['status' => 'error', 'mensaje' => 'Petición inválida'];

// --- LÓGICA DE CONSULTA ---
if (isset($_GET['semana']) && !empty($_GET['semana'])) {
    $semana = $_GET['semana'];
    $categoria = $_GET['categoria'] ?? 'todas';
    $staff_id = $_GET['staff_id'] ?? 'todos';

    // Parsear semana
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

            // Construcción de la consulta SQL
            $sql = "SELECT 
                        a.id,
                        to_char(a.fecha_permiso, 'DD/MM/YYYY') as fecha_permiso,
                        to_char(a.hora_salida, 'HH12:MI AM') as hora_salida,
                        a.duracion_horas,
                        p.nombre_completo,
                        p.categoria,
                        a.motivo
                    FROM autorizaciones_salida_staff a
                    JOIN profesores p ON a.profesor_id = p.id
                    WHERE a.fecha_permiso BETWEEN :fecha_inicio AND :fecha_fin";

            $params = [
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ];

            if ($categoria !== 'todas') {
                $sql .= " AND p.categoria = :categoria";
                $params[':categoria'] = $categoria;
            }

            if ($staff_id !== 'todos') {
                $sql .= " AND a.profesor_id = :staff_id";
                $params[':staff_id'] = $staff_id;
            }

            $sql .= " ORDER BY a.fecha_permiso, a.hora_salida";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'exito',
                'registros' => $registros,
                'debug' => [
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'sql' => $sql,
                    'params' => $params
                ]
            ];

        } catch (Exception $e) {
            $response['mensaje'] = 'Error al procesar la petición: ' . $e->getMessage();
        }
    } else {
        $response['mensaje'] = 'Formato de semana no válido.';
    }
} else {
    $response['mensaje'] = 'No se proporcionó el parámetro de semana.';
}

echo json_encode($response);
?>