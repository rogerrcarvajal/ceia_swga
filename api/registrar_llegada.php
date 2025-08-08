<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$input = json_decode(file_get_contents('php://input'), true);
$qr_id = filter_var($input['estudiante_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$qr_id) {
    echo json_encode(['status' => 'error', 'message' => 'Código QR inválido o no leído correctamente.']);
    exit();
}

try {
    $conn->beginTransaction();

    // Obtener período escolar activo
    $periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$periodo_activo) {
        throw new Exception("No hay un período escolar activo.");
    }
    $periodo_id = $periodo_activo['id'];

    // 1️⃣ Verificar si es estudiante
    $stmt_est = $conn->prepare("
        SELECT e.id, e.nombre_completo, e.apellido_completo, ep.grado_cursado
        FROM estudiantes e
        JOIN estudiante_periodo ep ON e.id = ep.estudiante_id
        WHERE e.id = :id AND ep.periodo_id = :pid
    ");
    $stmt_est->execute([':id' => $qr_id, ':pid' => $periodo_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

    if ($estudiante) {
        $fecha = date('m-d-y');
        $hora = date('H:i:s');
        $semana = date('W');
        $dia_semana = date('N');
        $es_tarde = strtotime($hora) > strtotime('08:05:59');

        // Validar que no esté repetido
        $stmt_check = $conn->prepare("SELECT id FROM llegadas_tarde WHERE estudiante_id = ? AND fecha_registro = ?");
        $stmt_check->execute([$qr_id, $fecha]);
        if ($stmt_check->fetch()) {
            throw new Exception("Este estudiante ya fue registrado hoy.");
        }

        // Registrar llegada
        $conn->prepare("
            INSERT INTO llegadas_tarde (estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$qr_id, $hora, $fecha, $semana, $dia_semana]);

        // STRIKES
        $mensaje = "Llegada registrada.";
        $conteo = 0;
        $observacion = "";

        if ($es_tarde) {
            $conn->prepare("
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
            ")->execute([$qr_id, $periodo_id, $semana]);

            $res = $conn->prepare("SELECT conteo_tardes, ultimo_mensaje FROM latepass_resumen_semanal WHERE estudiante_id = ? AND periodo_id = ? AND semana_del_anio = ?");
            $res->execute([$qr_id, $periodo_id, $semana]);
            $summary = $res->fetch(PDO::FETCH_ASSOC);

            $conteo = $summary['conteo_tardes'];
            $mensaje = $summary['ultimo_mensaje'];
            if ($conteo >= 3) {
                $observacion = "HA ALCANZADO EL MAXIMO DE STRIKE. PIERDE LA PRIMER HORA DE CLASE. DEBE LLAMAR A SU REPRESENTANTE";
            }
        }

        $conn->commit();

        echo json_encode([
            'status' => 'exito',
            'tipo' => 'estudiante',
            'nombre_completo' => $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'],
            'grado' => $estudiante['grado_cursado'],
            'hora_llegada' => $hora,
            'es_tarde' => $es_tarde,
            'conteo_tardes' => $conteo,
            'mensaje' => $mensaje,
            'observacion' => $observacion
        ]);
        exit();
    }

    // 2️⃣ Verificar si es staff
    if (esStaff($qr_id)) {
        $conn->commit();
        procesarStaff($qr_id, $conn);
        exit();
    }

    // 3️⃣ Verificar si es vehículo
    if (esVehiculo($qr_id)) {
        $conn->commit();
        procesarVehiculo($qr_id, $conn);
        exit();
    }

    // No reconocido
    throw new Exception("Código no reconocido. Verifique el QR.");

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}

function esStaff($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT 1 FROM profesores WHERE id = ?");
    $stmt->execute([$id]);
    return (bool)$stmt->fetch();
}

function esVehiculo($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT 1 FROM vehiculos WHERE id = ?");
    $stmt->execute([$id]);
    return (bool)$stmt->fetch();
}

function procesarStaff($id, $conn) {
    $stmt_prof = $conn->prepare("SELECT p.id, p.nombre_completo, pp.posicion FROM profesores p LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id WHERE p.id = ? ORDER BY pp.id DESC LIMIT 1");
    $stmt_prof->execute([$id]);
    $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);

    if (!$profesor) {
        echo json_encode(['status' => 'error', 'message' => 'Profesor no encontrado.']);
        return;
    }

    $hora = date('H:i:s');
    $fecha = date('Y-m-d');

    $conn->prepare("INSERT INTO movimientos_staff (profesor_id, hora_movimiento, fecha_movimiento) VALUES (?, ?, ?)")->execute([$id, $hora, $fecha]);

    echo json_encode([
        'status' => 'exito',
        'tipo' => 'staff',
        'nombre_completo' => $profesor['nombre_completo'],
        'posicion' => $profesor['posicion'] ?? 'No asignada',
        'hora' => $hora,
        'mensaje' => 'Ingreso registrado exitosamente.'
    ]);
}

function procesarVehiculo($id, $conn) {
    $stmt_veh = $conn->prepare("SELECT v.id, v.placa, v.modelo, e.apellido_completo FROM vehiculos v JOIN estudiantes e ON v.estudiante_id = e.id WHERE v.id = ?");
    $stmt_veh->execute([$id]);
    $vehiculo = $stmt_veh->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo) {
        echo json_encode(['status' => 'error', 'message' => 'Vehículo no encontrado o no autorizado.']);
        return;
    }

    $hora = date('H:i:s');
    $fecha = date('Y-m-d');

    $conn->prepare("INSERT INTO movimientos_vehiculos (vehiculo_id, hora_movimiento, fecha_movimiento) VALUES (?, ?, ?)")->execute([$id, $hora, $fecha]);

    echo json_encode([
        'status' => 'exito',
        'tipo' => 'vehiculo',
        'placa' => $vehiculo['placa'],
        'modelo' => $vehiculo['modelo'],
        'familia' => $vehiculo['apellido_completo'],
        'hora' => $hora,
        'mensaje' => 'Movimiento vehicular registrado.'
    ]);
}