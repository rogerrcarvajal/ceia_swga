<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo'])) {
    $response['message'] = 'Acceso denegado o código no proporcionado.';
    echo json_encode($response);
    exit();
}

try {
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'EST-') !== 0) {
        $response['message'] = 'El código QR no corresponde a un estudiante.';
        echo json_encode($response);
        exit();
    }

    $estudiante_id = (int) substr($codigo, 4);
    if ($estudiante_id <= 0) {
        $response['message'] = 'ID de estudiante inválido en el QR.';
        echo json_encode($response);
        exit();
    }
    
    $conn->beginTransaction();

    $periodo_activo_stmt = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");
    $periodo_activo = $periodo_activo_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$periodo_activo) {
        throw new Exception("No hay un período escolar activo configurado.");
    }
    $periodo_id = $periodo_activo['id'];

    $stmt_est = $conn->prepare("SELECT e.nombre_completo, e.apellido_completo FROM estudiantes e JOIN estudiante_periodo ep ON e.id = ep.estudiante_id WHERE e.id = :id AND ep.periodo_id = :pid");
    $stmt_est->execute([':id' => $estudiante_id, ':pid' => $periodo_id]);
    $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        throw new Exception("Estudiante no encontrado o no asignado al período activo.");
    }

    // Usar el timestamp del cliente si está disponible, si no, usar la hora del servidor
    if (isset($_POST['timestamp'])) {
        $dt = new DateTime($_POST['timestamp']);
        $dt->setTimezone(new DateTimeZone('America/Caracas')); // Ajustar a la zona horaria del servidor
    } else {
        $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    }
    $fecha = $dt->format("Y-m-d");
    $hora_actual = $dt->format("H:i:s");
    $semana_del_anio = $dt->format("W");

    $nombre_completo = $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'];

    $stmt_check = $conn->prepare("SELECT id FROM llegadas_tarde WHERE estudiante_id = :id AND fecha_registro = :fecha");
    $stmt_check->execute([':id' => $estudiante_id, ':fecha' => $fecha]);

    if ($stmt_check->fetch()) {
        throw new Exception("{$nombre_completo} ya registró su llegada hoy.");
    }
    
    $es_tarde = ($hora_actual > "08:05:59");

    $ins = $conn->prepare("INSERT INTO llegadas_tarde (estudiante_id, fecha_registro, hora_llegada, semana_del_anio) VALUES (:est_id, :fecha, :hora, :semana)");
    $ins->execute([':est_id' => $estudiante_id, ':fecha' => $fecha, ':hora' => $hora_actual, ':semana' => $semana_del_anio]);

    $mensaje_final = "✅ Llegada puntual registrada para {$nombre_completo}.";

    if ($es_tarde) {
        $stmt_strike = $conn->prepare("SELECT id, conteo_tardes FROM latepass_resumen_semanal WHERE estudiante_id = :id AND semana_del_anio = :semana AND periodo_id = :pid");
        $stmt_strike->execute([':id' => $estudiante_id, ':semana' => $semana_del_anio, ':pid' => $periodo_id]);
        $resumen = $stmt_strike->fetch(PDO::FETCH_ASSOC);

        $nuevo_conteo = 1;
        if ($resumen) {
            $nuevo_conteo = $resumen['conteo_tardes'] + 1;
            $upd = $conn->prepare("UPDATE latepass_resumen_semanal SET conteo_tardes = :conteo WHERE id = :id");
            $upd->execute([':conteo' => $nuevo_conteo, ':id' => $resumen['id']]);
        } else {
            $ins_strike = $conn->prepare("INSERT INTO latepass_resumen_semanal (estudiante_id, periodo_id, semana_del_anio, conteo_tardes) VALUES (:est_id, :pid, :semana, 1)");
            $ins_strike->execute([':est_id' => $estudiante_id, ':pid' => $periodo_id, ':semana' => $semana_del_anio]);
        }
        $mensaje_final = "⚠️ LLEGADA TARDE para {$nombre_completo}. Strike semanal #{$nuevo_conteo}.";
    }
    
    $conn->commit();
    $response['success'] = true;
    $response['message'] = $mensaje_final;

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);