<?php
error_reporting(0);
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

    if ($registro_existente) {
        $hora_registrada = $registro_existente['hora_llegada'];
        $mensaje_duplicado = "✅ LLEGADA YA REGISTRADA a las {$hora_registrada} para {$nombre_completo}.";

        $response['success'] = true;
        $response['message'] = $mensaje_duplicado;
        $response['data'] = [
            'tipo' => 'EST',
            'nombre_completo' => $nombre_completo,
            'grado' => $estudiante['grado_cursado'],
            'hora_registrada' => $hora_registrada,
            'strike_count' => 0, 
            'strike_level' => 0, 
            'mensaje_especial' => 'Registro duplicado.'
        ];
        echo json_encode($response);
        exit();
    }

    // --- LÓGICA SIMPLIFICADA PARA DEPURACIÓN ---
    $ins = $conn->prepare("INSERT INTO llegadas_tarde (estudiante_id, fecha_registro, hora_llegada, semana_del_anio) VALUES (:est_id, :fecha, :hora, :semana)");
    $ins->execute([
        ':est_id' => $estudiante_id, 
        ':fecha' => $fecha_actual, 
        ':hora' => $hora_actual, 
        ':semana' => $dt_now->format("W")
    ]);

    $mensaje_final = "✅ Llegada registrada (versión simplificada).";

    $conn->commit();

    $response['success'] = true;
    $response['message'] = $mensaje_final;
    $response['data'] = [
        'tipo' => 'EST',
        'nombre_completo' => $nombre_completo,
        'grado' => $estudiante['grado_cursado'],
        'hora_registrada' => $hora_actual,
        'strike_count' => 0,
        'strike_level' => 0,
        'mensaje_especial' => 'Lógica de strikes desactivada temporalmente para depuración.'
    ];

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
