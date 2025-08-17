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
    
    // Obtener período activo e información del estudiante
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

    // Establecer zona horaria y obtener fecha/hora actual
    $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    $hora_actual = $dt->format("H:i:s");
    $fecha_actual = $dt->format("Y-m-d");

    // --- LÓGICA DE NEGOCIO CORREGIDA ---
    $conn->beginTransaction();

    // 1. Verificar si ya tiene un registro de llegada HOY para evitar duplicados
    $stmt_check = $conn->prepare("SELECT id FROM llegadas_tarde WHERE estudiante_id = :id AND fecha_registro = :fecha");
    $stmt_check->execute([':id' => $estudiante_id, ':fecha' => $fecha_actual]);
    if ($stmt_check->fetch()) {
        throw new Exception("{$nombre_completo} ya registró su llegada hoy.");
    }

    // 2. Insertar el registro de llegada para TODOS los estudiantes
    $ins = $conn->prepare("INSERT INTO llegadas_tarde (estudiante_id, fecha_registro, hora_llegada, semana_del_anio) VALUES (:est_id, :fecha, :hora, :semana)");
    $ins->execute([
        ':est_id' => $estudiante_id, 
        ':fecha' => $fecha_actual, 
        ':hora' => $hora_actual, 
        ':semana' => $dt->format("W")
    ]);

    $strike_count = 0;
    $mensaje_final = "✅ Llegada a tiempo registrada para {$nombre_completo}.";

    // 3. Si es tarde, calcular los strikes. Si no, el strike_count se queda en 0.
    if ($hora_actual > "08:06:00") {
        // Calcular strikes semanales (de Lunes a Domingo), contando solo las llegadas TARDE
        $day_of_week = $dt->format('N'); // 1 (Lunes) a 7 (Domingo)
        $start_of_week_date = (clone $dt)->modify('-' . ($day_of_week - 1) . ' days')->format('Y-m-d');
        $end_of_week_date = (clone $dt)->modify('+' . (7 - $day_of_week) . ' days')->format('Y-m-d');

        $stmt_strikes = $conn->prepare(
            "SELECT COUNT(*) as count FROM llegadas_tarde WHERE estudiante_id = :id AND fecha_registro BETWEEN :start_week AND :end_week AND hora_llegada > '08:06:00'"
        );
        $stmt_strikes->execute([':id' => $estudiante_id, ':start_week' => $start_of_week_date, ':end_week' => $end_of_week_date]);
        $strike_count = (int) $stmt_strikes->fetch(PDO::FETCH_ASSOC)['count'];
        
        $mensaje_final = "⚠️ LLEGADA TARDE para {$nombre_completo}. Strike semanal #{$strike_count}.";
    }

    $conn->commit();

    // 4. Preparar respuesta exitosa
    $response['success'] = true;
    $response['message'] = $mensaje_final;
    $response['data'] = [
        'tipo' => 'EST',
        'nombre_completo' => $nombre_completo,
        'grado' => $estudiante['grado_cursado'],
        'hora_registrada' => $hora_actual,
        'strike_count' => $strike_count
    ];

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);