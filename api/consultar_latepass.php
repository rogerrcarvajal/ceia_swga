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
                    TO_CHAR(lt.fecha_registro, 'MM-DD-YYYY') as fecha_registro, 
                    lt.hora_llegada,
                    CAST((SELECT COUNT(*) FROM llegadas_tarde WHERE estudiante_id = lt.estudiante_id AND semana_del_anio = :semana AND CAST(hora_llegada AS TIME) > '08:05:59') AS INTEGER) as conteo_tardes,
                    rs.ultimo_mensaje
                FROM estudiante_periodo ep
                JOIN estudiantes e ON ep.estudiante_id = e.id
                INNER JOIN llegadas_tarde lt ON ep.estudiante_id = lt.estudiante_id AND lt.semana_del_anio = :semana
                LEFT JOIN latepass_resumen_semanal rs ON ep.estudiante_id = rs.estudiante_id AND rs.semana_del_anio = :semana AND rs.periodo_id = ep.periodo_id
                WHERE ep.periodo_id = :pid";
        
        $params = [':semana' => $semana, ':pid' => $periodo_id];

        if ($grado !== 'todos' && !empty($grado)) {
            $sql .= " AND ep.grado_cursado = :grado";
            $params[':grado'] = $grado;
        }

        $sql .= " ORDER BY lt.fecha_registro DESC, lt.hora_llegada DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($registros as &$reg) {
            if ($reg['conteo_tardes'] >= 3) {
                $reg['ultimo_mensaje'] = 'Limite de Strike alcanzado. Pierde la 1era. Hora de clases. Contactar a su representante';
            }
        }

        $response = ['status' => 'exito', 'registros' => $registros];

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
