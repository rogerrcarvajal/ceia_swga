<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Decodificar el ID del estudiante enviado desde JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$estudiante_id = filter_var($input['estudiante_id'] ?? 0, FILTER_VALIDATE_INT);

// Preparamos una respuesta por defecto en caso de error inicial
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if (!$estudiante_id) {
    echo json_encode($response);
    exit();
}

try {
    $conn->beginTransaction(); // Iniciar transacción para asegurar que todo se ejecute o nada

    // --- 1. Obtener datos clave ---
    $periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$periodo_activo) {
        throw new Exception("No hay un período escolar activo configurado.");
    }
    $periodo_id = $periodo_activo['id'];

    $stmt_est = $conn->prepare("SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado FROM estudiantes e JOIN estudiante_periodo ep ON e.id = ep.estudiante_id WHERE e.id = :eid AND ep.periodo_id = :pid");
    $stmt_est->execute([':eid' => $estudiante_id, ':pid' => $periodo_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);
    if (!$estudiante) {
        throw new Exception("Estudiante no encontrado o no asignado al período activo.");
    }

    // --- 2. VALIDACIÓN: Evitar registros duplicados por día ---
    $fecha_registro = date('Y-m-d');
    $stmt_check = $conn->prepare("SELECT id FROM llegadas_tarde WHERE estudiante_id = ? AND fecha_registro = ?");
    $stmt_check->execute([$estudiante_id, $fecha_registro]);
    if ($stmt_check->fetch()) {
        throw new Exception("Este estudiante ya fue registrado hoy (" . date('H:i:s') . ").");
    }
    
    // --- 3. Definir variables de tiempo y registrar la llegada ---
    $hora_llegada = date('H:i:s');
    $semana_del_anio = date('W');
    $dia_de_la_semana = date('N'); 
    $es_tarde = (strtotime($hora_llegada) > strtotime('08:05:59'));

    $sql_insert = "INSERT INTO llegadas_tarde (estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana) VALUES (?, ?, ?, ?, ?)";
    $conn->prepare($sql_insert)->execute([$estudiante_id, $hora_llegada, $fecha_registro, $semana_del_anio, $dia_de_la_semana]);

    // --- 4. Lógica de Alertas y Strikes (MEJORADA) ---
    $conteo_tardes = 0;
    $mensaje = "Llegada a tiempo registrada.";

    if ($es_tarde) {
        // Usamos una sola consulta para insertar o actualizar el conteo semanal (más eficiente y seguro)
        $sql_upsert = "
            INSERT INTO latepass_resumen_semanal (estudiante_id, periodo_id, semana_del_anio, conteo_tardes, ultimo_mensaje)
            VALUES (?, ?, ?, 1, 'Llegada Tarde - Primer Strike')
            ON CONFLICT (estudiante_id, periodo_id, semana_del_anio)
            DO UPDATE SET 
                conteo_tardes = latepass_resumen_semanal.conteo_tardes + 1,
                ultimo_mensaje = CASE
                                   WHEN latepass_resumen_semanal.conteo_tardes + 1 = 2 THEN 'Llegada Tarde - Segundo Strike'
                                   ELSE 'Llegada Tarde - TERCER STRIKE. Notificar al representante.'
                                 END;
        ";
        $conn->prepare($sql_upsert)->execute([$estudiante_id, $periodo_id, $semana_del_anio]);

        // Obtenemos el conteo y mensaje actualizados
        $stmt_current = $conn->prepare("SELECT conteo_tardes, ultimo_mensaje FROM latepass_resumen_semanal WHERE estudiante_id = ? AND periodo_id = ? AND semana_del_anio = ?");
        $stmt_current->execute([$estudiante_id, $periodo_id, $semana_del_anio]);
        $resumen_actual = $stmt_current->fetch(PDO::FETCH_ASSOC);
        $conteo_tardes = $resumen_actual['conteo_tardes'];
        $mensaje = $resumen_actual['ultimo_mensaje'];
    }

    $conn->commit(); // Confirmar todos los cambios en la base de datos

    // --- 5. Preparar la respuesta JSON para el frontend ---
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
    if ($conn->inTransaction()) {
        $conn->rollBack(); // Revertir cambios si algo falla
    }
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit(); // Asegurarse de que no se imprima nada más
?>