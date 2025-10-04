<?php
session_start();
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Leer el cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

// Validar rol de usuario
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

try {
    // Recoger datos comunes
    $estudiante_id = $input['estudiante_id'] ?? null;
    $fecha_salida = $input['fecha_salida'] ?? null;
    $hora_salida = $input['hora_salida'] ?? null;
    $motivo = htmlspecialchars($input['motivo'] ?? '', ENT_QUOTES, 'UTF-8');
    $registrado_por_usuario_id = $_SESSION['usuario']['id'] ?? null;
    $autorizado_por = $input['autorizado_por'] ?? null;

    $retirado_por_nombre = null;
    $retirado_por_parentesco = null;

    if ($autorizado_por === 'padre') {
        $padre_id = $input['padre_id'] ?? null;
        if (!$padre_id) throw new Exception('ID del padre no proporcionado.');
        $stmt = $conn->prepare("SELECT nombre_completo FROM padres WHERE padre_id = :id");
        $stmt->execute(['id' => $padre_id]);
        $representante = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$representante) throw new Exception('Padre no encontrado.');
        $retirado_por_nombre = $representante['nombre_completo'];
        $retirado_por_parentesco = 'Padre';
    } elseif ($autorizado_por === 'madre') {
        $madre_id = $input['madre_id'] ?? null;
        if (!$madre_id) throw new Exception('ID de la madre no proporcionado.');
        $stmt = $conn->prepare("SELECT nombre_completo FROM madres WHERE madre_id = :id");
        $stmt->execute(['id' => $madre_id]);
        $representante = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$representante) throw new Exception('Madre no encontrada.');
        $retirado_por_nombre = $representante['nombre_completo'];
        $retirado_por_parentesco = 'Madre';
    } elseif ($autorizado_por === 'otro') {
        $retirado_por_nombre = htmlspecialchars($input['retirado_por_nombre'] ?? null, ENT_QUOTES, 'UTF-8');
        $retirado_por_parentesco = htmlspecialchars($input['retirado_por_parentesco'] ?? null, ENT_QUOTES, 'UTF-8');
    } else {
        throw new Exception('Debe seleccionar un tipo de persona autorizada.');
    }

    if (!$estudiante_id || !$fecha_salida || !$hora_salida || !$retirado_por_nombre || !$retirado_por_parentesco || !$registrado_por_usuario_id) {
        throw new Exception('Datos incompletos para procesar la solicitud.');
    }

    $sql = "INSERT INTO autorizaciones_salida (estudiante_id, fecha_salida, hora_salida, retirado_por_nombre, retirado_por_parentesco, motivo, registrado_por_usuario_id) VALUES (:estudiante_id, :fecha_salida, :hora_salida, :retirado_por_nombre, :retirado_por_parentesco, :motivo, :registrado_por_usuario_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':estudiante_id' => $estudiante_id,
        ':fecha_salida' => $fecha_salida,
        ':hora_salida' => $hora_salida,
        ':retirado_por_nombre' => $retirado_por_nombre,
        ':retirado_por_parentesco' => $retirado_por_parentesco,
        ':motivo' => $motivo,
        ':registrado_por_usuario_id' => $registrado_por_usuario_id
    ]);
    
    $salida_id = $conn->lastInsertId();
    
    if ($salida_id) {
        echo json_encode(['success' => true, 'salida_id' => $salida_id, 'message' => 'Autorización guardada correctamente.']);
    } else {
        throw new Exception('No se pudo guardar la autorización.');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>