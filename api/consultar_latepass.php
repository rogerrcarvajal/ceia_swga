<?php
session_start();
// --- BLOQUE DE SEGURIDAD AÑADIDO ---
if (!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit();
}

require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Filtros no válidos.'];
$semana = filter_var($_GET['semana'] ?? null, FILTER_VALIDATE_INT);
$grado = $_GET['grado'] ?? 'todos';

if ($semana) {
    try {
        $periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if (!$periodo_activo) throw new Exception("No hay período escolar activo.");
        $periodo_id = $periodo_activo['id'];

        $sql = "SELECT 
                    e.nombre_completo, e.apellido_completo, ep.grado_cursado,
<<<<<<< HEAD
                    TO_CHAR(lt.fecha_registro, 'MM-DD-YYYY') as fecha_registro, 
                    lt.hora_llegada,
=======
                    TO_CHAR(lt.fecha_registro, 'YYYY-MM-DD') as fecha_registro, lt.hora_llegada,
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec
                    COALESCE(rs.conteo_tardes, 0) as conteo_tardes,
                    rs.ultimo_mensaje
                FROM llegadas_tarde lt
                JOIN estudiantes e ON lt.estudiante_id = e.id
                JOIN estudiante_periodo ep ON e.id = ep.estudiante_id AND ep.periodo_id = :pid
                LEFT JOIN latepass_resumen_semanal rs ON lt.estudiante_id = rs.estudiante_id AND rs.semana_del_anio = lt.semana_del_anio AND rs.periodo_id = :pid
                WHERE lt.semana_del_anio = :semana AND ep.periodo_id = :pid";
        
        $params = [':semana' => $semana, ':pid' => $periodo_id];

        if ($grado !== 'todos' && !empty($grado)) {
            $sql .= " AND ep.grado_cursado = :grado";
            $params[':grado'] = $grado;
        }

        $sql .= " ORDER BY lt.fecha_registro DESC, lt.hora_llegada DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response = ['status' => 'exito', 'registros' => $registros];

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
