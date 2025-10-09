<?php
session_start();
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// --- CONTROL DE ACCESO Y VALIDACIÓN INICIAL ---
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'mensaje' => 'Acceso denegado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido.']);
    exit();
}

// --- LÓGICA PARA GUARDAR LA AUTORIZACIÓN ---
try {
    // Recoger y sanitizar datos del formulario
    $profesor_id = filter_input(INPUT_POST, 'profesor_id', FILTER_VALIDATE_INT);
    $periodo_id = filter_input(INPUT_POST, 'periodo_id', FILTER_VALIDATE_INT);
    $registrado_por_usuario_id = filter_input(INPUT_POST, 'registrado_por_usuario_id', FILTER_VALIDATE_INT);
    $fecha_permiso = $_POST['fecha_permiso'] ?? null;
    $hora_salida = $_POST['hora_salida'] ?? null;
    $duracion_horas = filter_input(INPUT_POST, 'duracion_horas', FILTER_VALIDATE_FLOAT);
    $motivo = htmlspecialchars($_POST['motivo'] ?? '', ENT_QUOTES, 'UTF-8');

    // Validar datos obligatorios
    if (!$profesor_id || !$periodo_id || !$registrado_por_usuario_id || !$fecha_permiso || !$hora_salida || !$duracion_horas) {
        throw new Exception('Todos los campos obligatorios deben ser completados.');
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO autorizaciones_salida_staff 
                (profesor_id, periodo_id, registrado_por_usuario_id, fecha_permiso, hora_salida, duracion_horas, motivo) 
            VALUES 
                (:profesor_id, :periodo_id, :registrado_por_usuario_id, :fecha_permiso, :hora_salida, :duracion_horas, :motivo)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':profesor_id' => $profesor_id,
        ':periodo_id' => $periodo_id,
        ':registrado_por_usuario_id' => $registrado_por_usuario_id,
        ':fecha_permiso' => $fecha_permiso,
        ':hora_salida' => $hora_salida,
        ':duracion_horas' => $duracion_horas,
        ':motivo' => $motivo
    ]);

    $nuevaAutorizacionId = $conn->lastInsertId();

    if ($nuevaAutorizacionId) {
        echo json_encode([
            'status' => 'exito',
            'mensaje' => 'Autorización de salida para el personal guardada exitosamente.',
            'id' => $nuevaAutorizacionId
        ]);
    } else {
        throw new Exception('No se pudo guardar el registro en la base de datos.');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
?>