<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Decodificar el ID del estudiante enviado desde JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$estudiante_id = $input['estudiante_id'] ?? 0;

if (!$estudiante_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de estudiante no proporcionado.']);
    exit();
}

try {
    // --- 1. Obtener datos del período activo y del estudiante ---
    $periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$periodo_activo) throw new Exception("No hay período escolar activo.");
    $periodo_id = $periodo_activo['id'];

    $stmt_est = $conn->prepare("SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado FROM estudiantes e JOIN estudiante_periodo ep ON e.id = ep.estudiante_id WHERE e.id = :eid AND ep.periodo_id = :pid");
    $stmt_est->execute([':eid' => $estudiante_id, ':pid' => $periodo_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);
    if (!$estudiante) throw new Exception("Estudiante no encontrado o no asignado al período activo.");

    // --- 2. Definir variables de tiempo ---
    $hora_llegada = date('H:i:s');
    $fecha_registro = date('Y-m-d');
    $semana_del_anio = date('W');
    $dia_de_la_semana = date('N'); // 1 (para Lunes) hasta 7 (para Domingo)
    $es_tarde = (strtotime($hora_llegada) > strtotime('08:05:59'));

    // --- 3. Insertar el registro individual ---
    $sql_insert = "INSERT INTO llegadas_tarde (estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana) VALUES (?, ?, ?, ?, ?)";
    $conn->prepare($sql_insert)->execute([$estudiante_id, $hora_llegada, $fecha_registro, $semana_del_anio, $dia_de_la_semana]);

    // --- 4. Lógica de Alertas y Strikes ---
    $conteo_tardes = 0;
    $mensaje = "Llegada a tiempo registrada.";

    if ($es_tarde) {
        // Buscar o crear el registro de resumen semanal
        $stmt_resumen = $conn->prepare("SELECT id, conteo_tardes FROM latepass_resumen_semanal WHERE estudiante_id = ? AND periodo_id = ? AND semana_del_anio = ?");
        $stmt_resumen->execute([$estudiante_id, $periodo_id, $semana_del_anio]);
        $resumen = $stmt_resumen->fetch();

        if ($resumen) {
            // Ya tiene llegadas tarde esta semana, actualizar el conteo
            $conteo_tardes = $resumen['conteo_tardes'] + 1;
            $sql_update = "UPDATE latepass_resumen_semanal SET conteo_tardes = ? WHERE id = ?";
            $conn->prepare($sql_update)->execute([$conteo_tardes, $resumen['id']]);
        } else {
            // Es su primera llegada tarde de la semana, crear el registro
            $conteo_tardes = 1;
            $sql_insert_resumen = "INSERT INTO latepass_resumen_semanal (estudiante_id, periodo_id, semana_del_anio, conteo_tardes) VALUES (?, ?, ?, ?)";
            $conn->prepare($sql_insert_resumen)->execute([$estudiante_id, $periodo_id, $semana_del_anio, $conteo_tardes]);
        }

        // Definir el mensaje según el número de strikes
        if ($conteo_tardes == 1) $mensaje = "Llegada Tarde - Primer Strike";
        elseif ($conteo_tardes == 2) $mensaje = "Llegada Tarde - Segundo Strike";
        else $mensaje = "Llegada Tarde - TERCER STRIKE. Notificar al representante.";
    }

    // --- 5. Preparar la respuesta JSON ---
    $response = [
        'status' => 'exito',
        'nombre_completo' => $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'],
        'grado' => $estudiante['grado_cursado'],
        'hora_llegada' => $hora_llegada,
        'es_tarde' => $es_tarde,
        'conteo_tardes' => $conteo_tardes,
        'mensaje' => $mensaje
    ];

} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>