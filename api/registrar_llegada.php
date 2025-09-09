<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo'])) {
    $response['message'] = 'Acceso denegado o código no proporcionado.';
    echo json_encode($response);
    exit();
}

try {
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'EST-') !== 0) {
        throw new Exception('El código QR no corresponde a un estudiante.');
    }

    $estudiante_id = (int) substr($codigo, 4);
    if ($estudiante_id <= 0) {
        throw new Exception('ID de estudiante inválido en el QR.');
    }
    
    $periodo_activo_stmt = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
    $periodo_activo = $periodo_activo_stmt->fetch(PDO::FETCH_ASSOC);
    if (!$periodo_activo) {
        throw new Exception("No hay un período escolar activo configurado.");
    }
    $periodo_id = $periodo_activo['id'];

    $stmt_est = $conn->prepare("SELECT e.nombre_completo, e.apellido_completo, ep.grado_cursado FROM estudiantes e JOIN estudiante_periodo ep ON e.id = ep.estudiante_id WHERE e.id = :id AND ep.periodo_id = :pid");
    $stmt_est->execute([':id' => $estudiante_id, ':pid' => $periodo_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);
    if (!$estudiante) {
        throw new Exception("Estudiante no encontrado o no asignado al período activo.");
    }
    $nombre_completo = $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'];

    $dt_zone = new DateTimeZone('America/Caracas');
    $dt_now = new DateTime('now', $dt_zone);
    $late_threshold_time = '08:05:59';
    
    $fecha_actual = $dt_now->format("Y-m-d");
    $hora_actual = $dt_now->format("H:i:s");

    $conn->beginTransaction();

    $stmt_check = $conn->prepare("SELECT hora_llegada FROM llegadas_tarde WHERE estudiante_id = :id AND fecha_registro = :fecha");
    $stmt_check->execute([':id' => $estudiante_id, ':fecha' => $fecha_actual]);
    $registro_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

    $late_query_string = "SELECT COUNT(*) as count FROM llegadas_tarde WHERE estudiante_id = :id AND fecha_registro BETWEEN :start_week AND :end_week AND CAST(hora_llegada AS TIME) > '{$late_threshold_time}'";

    if ($registro_existente) {
        $hora_registrada = $registro_existente['hora_llegada'];
        $strike_count = 0;
        $strike_level = 0;
        $mensaje_especial = '';
        $mensaje_duplicado = "✅ LLEGADA YA REGISTRADA a las {$hora_registrada} para {$nombre_completo}.";

        if (strtotime($hora_registrada) > strtotime($late_threshold_time)) {
            $day_of_week = $dt_now->format('N');
            $start_of_week_date = (clone $dt_now)->modify('-' . ($day_of_week - 1) . ' days')->format('Y-m-d');
            $end_of_week_date = (clone $dt_now)->modify('+' . (7 - $day_of_week) . ' days')->format('Y-m-d');

            $stmt_strikes = $conn->prepare($late_query_string);
            $stmt_strikes->execute([':id' => $estudiante_id, ':start_week' => $start_of_week_date, ':end_week' => $end_of_week_date]);
            $strike_count = (int) $stmt_strikes->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($strike_count == 1) $strike_level = 1;
            elseif ($strike_count == 2) $strike_level = 2;
            elseif ($strike_count >= 3) $strike_level = 3;

            if ($strike_level >= 3) {
                $mensaje_especial = 'El Estudiante ha acumulado 3 o más strikes, por ende pierde la primera hora de clase y debe contactar a su representante.';
            }
            $mensaje_duplicado = "⚠️ LLEGADA TARDE (ya registrada a las {$hora_registrada}). Strike semanal #{$strike_count} para {$nombre_completo}.";
        }

        $response['success'] = true;
        $response['message'] = $mensaje_duplicado;
        $response['data'] = [
            'tipo' => 'EST',
            'nombre_completo' => $nombre_completo,
            'grado' => $estudiante['grado_cursado'],
            'hora_registrada' => $hora_registrada,
            'strike_count' => $strike_count,
            'strike_level' => $strike_level,
            'mensaje_especial' => $mensaje_especial
        ];
        echo json_encode($response);
        exit();
    }

    $ins = $conn->prepare("INSERT INTO llegadas_tarde (estudiante_id, fecha_registro, hora_llegada, semana_del_anio) VALUES (:est_id, :fecha, :hora, :semana)");
    $ins->execute([
        ':est_id' => $estudiante_id, 
        ':fecha' => $fecha_actual, 
        ':hora' => $hora_actual, 
        ':semana' => $dt_now->format("W")
    ]);

    $strike_count = 0;
    $strike_level = 0;
    $mensaje_especial = '';
    $mensaje_final = "✅ Llegada a tiempo registrada para {$nombre_completo}.";

    if (strtotime($hora_actual) > strtotime($late_threshold_time)) {
        $day_of_week = $dt_now->format('N');
        $start_of_week_date = (clone $dt_now)->modify('-' . ($day_of_week - 1) . ' days')->format('Y-m-d');
        $end_of_week_date = (clone $dt_now)->modify('+' . (7 - $day_of_week) . ' days')->format('Y-m-d');

        $stmt_strikes = $conn->prepare($late_query_string);
        $stmt_strikes->execute([':id' => $estudiante_id, ':start_week' => $start_of_week_date, ':end_week' => $end_of_week_date]);
        $strike_count = (int) $stmt_strikes->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($strike_count == 1) $strike_level = 1;
        elseif ($strike_count == 2) $strike_level = 2;
        elseif ($strike_count >= 3) $strike_level = 3;

        if ($strike_level >= 3) {
            $mensaje_especial = 'El Estudiante ha acumulado 3 o más strikes, por ende pierde la primera hora de clase y debe contactar a su representante.';
        }
        
        $mensaje_final = "⚠️ LLEGADA TARDE para {$nombre_completo}. Strike semanal #{$strike_count}.";
    }

    // Prepara el mensaje final que se guardará en el resumen
    $mensaje_resumen = $mensaje_especial ?: $mensaje_final;

    // Sentencia SQL para insertar o actualizar el resumen semanal
    $sql_resumen = "INSERT INTO latepass_resumen_semanal (estudiante_id, periodo_id, semana_del_anio, anio, conteo_tardes, ultimo_mensaje, fecha_actualizacion)
                    VALUES (:est_id, :pid, :semana, :anio, :conteo, :mensaje, :fecha_act)
                    ON CONFLICT (estudiante_id, periodo_id, semana_del_anio, anio) 
                    DO UPDATE SET 
                        conteo_tardes = EXCLUDED.conteo_tardes,
                        ultimo_mensaje = EXCLUDED.ultimo_mensaje,
                        fecha_actualizacion = EXCLUDED.fecha_actualizacion";
    
    $stmt_resumen = $conn->prepare($sql_resumen);
    $stmt_resumen->execute([
        ':est_id' => $estudiante_id,
        ':pid' => $periodo_id,
        ':semana' => $dt_now->format("W"),
        ':anio' => $dt_now->format("Y"),
        ':conteo' => $strike_count,
        ':mensaje' => $mensaje_resumen,
        ':fecha_act' => $dt_now->format('Y-m-d H:i:s')
    ]);


    $conn->commit();

    $response['success'] = true;
    $response['message'] = $mensaje_final;
    $response['data'] = [
        'tipo' => 'EST',
        'nombre_completo' => $nombre_completo,
        'grado' => $estudiante['grado_cursado'],
        'hora_registrada' => $hora_actual,
        'strike_count' => $strike_count,
        'strike_level' => $strike_level,
        'mensaje_especial' => $mensaje_especial
    ];

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);