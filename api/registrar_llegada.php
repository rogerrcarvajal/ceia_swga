<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Entrada del QR (ID crudo)
$input = json_decode(file_get_contents('php://input'), true);
$qr_id = filter_var($input['estudiante_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$qr_id) {
    echo json_encode(['status' => 'error', 'message' => 'Código QR inválido o no leído correctamente.']);
    exit();
}

try {
    $conn->beginTransaction();

    // Obtener período activo
    $periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$periodo_activo) {
        throw new Exception("No hay un período escolar activo.");
    }
    $periodo_id = $periodo_activo['id'];

    // --- CASO 1: ESTUDIANTE ---
    $stmt_est = $conn->prepare("
        SELECT e.id, e.nombre_completo, e.apellido_completo, ep.grado_cursado
        FROM estudiantes e
        JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
        WHERE e.id = :id AND ep.periodo_id = :pid
    ");
    $stmt_est->execute([':id' => $qr_id, ':pid' => $periodo_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

    if ($estudiante) {
        // Validar que no se registre dos veces el mismo día
        $fecha = date('Y-m-d');
        $stmt_check = $conn->prepare("SELECT id FROM llegadas_tarde WHERE estudiante_id = ? AND fecha_registro = ?");
        $stmt_check->execute([$qr_id, $fecha]);
        if ($stmt_check->fetch()) {
            throw new Exception("Este estudiante ya fue registrado hoy.");
        }

        $hora = date('H:i:s');
        $semana = date('W');
        $dia_semana = date('N');
        $es_tarde = strtotime($hora) > strtotime('08:05:59');

        $conn->prepare("
            INSERT INTO llegadas_tarde (estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$qr_id, $hora, $fecha, $semana, $dia_semana]);

        // Manejo de strikes
        $mensaje = "Llegada registrada.";
        $conteo = 0;

        if ($es_tarde) {
            $sql_strikes = "
                INSERT INTO latepass_resumen_semanal (estudiante_id, periodo_id, semana_del_anio, conteo_tardes, ultimo_mensaje)
                VALUES (?, ?, ?, 1, 'Llegada Tarde - Primer Strike')
                ON CONFLICT (estudiante_id, periodo_id, semana_del_anio)
                DO UPDATE SET 
                    conteo_tardes = latepass_resumen_semanal.conteo_tardes + 1,
                    ultimo_mensaje = CASE
                        WHEN latepass_resumen_semanal.conteo_tardes + 1 = 2 THEN 'Llegada Tarde - Segundo Strike'
                        WHEN latepass_resumen_semanal.conteo_tardes + 1 >= 3 THEN 'Llegada Tarde - TERCER STRIKE. No puede entrar a la 1ra hora.'
                        ELSE 'Llegada Tarde'
                    END
            ";
            $conn->prepare($sql_strikes)->execute([$qr_id, $periodo_id, $semana]);

            $res = $conn->prepare("
                SELECT conteo_tardes, ultimo_mensaje
                FROM latepass_resumen_semanal
                WHERE estudiante_id = ? AND periodo_id = ? AND semana_del_anio = ?
            ");
            $res->execute([$qr_id, $periodo_id, $semana]);
            $summary = $res->fetch(PDO::FETCH_ASSOC);
            $conteo = $summary['conteo_tardes'];
            $mensaje = $summary['ultimo_mensaje'];
        }

        $conn->commit();

        echo json_encode([
            'status' => 'exito',
            'nombre_completo' => $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'],
            'grado' => $estudiante['grado_cursado'],
            'hora_llegada' => $hora,
            'es_tarde' => $es_tarde,
            'conteo_tardes' => $conteo,
            'mensaje' => $mensaje
        ]);
        exit();
    }

    // --- CASO 2: STAFF ---
    $stmt_staff = $conn->prepare("SELECT id FROM profesores WHERE id = ?");
    $stmt_staff->execute([$qr_id]);
    if ($stmt_staff->fetch()) {
        $conn->commit(); // Cerramos transacción aquí antes de delegar
        require_once __DIR__ . '/registrar_movimiento_staff.php';
        exit();
    }

    // --- CASO 3: VEHÍCULO ---
    $stmt_veh = $conn->prepare("SELECT id FROM vehiculos WHERE id = ?");
    $stmt_veh->execute([$qr_id]);
    if ($stmt_veh->fetch()) {
        $conn->commit();
        require_once __DIR__ . '/registrar_movimiento_vehiculo.php';
        exit();
    }

    // --- CASO 4: NO COINCIDE CON NINGÚN REGISTRO ---
    throw new Exception("Código no reconocido. Verifique el QR.");

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}