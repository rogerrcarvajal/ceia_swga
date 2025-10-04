<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Leer el cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

// Validar rol de usuario
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

// Recoger los datos del input
$estudiante_id = $input['estudiante_id'] ?? null;
$fecha_salida = $input['fecha_salida'] ?? null;
$hora_salida = $input['hora_salida'] ?? null;
$retirado_por_nombre = $input['retirado_por_nombre'] ?? null;
$retirado_por_parentesco = $input['retirado_por_parentesco'] ?? null;
$motivo = $input['motivo'] ?? null;
$registrado_por_usuario_id = $_SESSION['usuario']['id'] ?? null;

// Validar datos básicos
if (!$estudiante_id || !$fecha_salida || !$hora_salida || !$retirado_por_nombre || !$registrado_por_usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit();
}

try {
    $sql = "INSERT INTO autorizaciones_salida 
                (estudiante_id, fecha_salida, hora_salida, retirado_por_nombre, retirado_por_parentesco, motivo, registrado_por_usuario_id) 
            VALUES 
                (:estudiante_id, :fecha_salida, :hora_salida, :retirado_por_nombre, :retirado_por_parentesco, :motivo, :registrado_por_usuario_id)";
    
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
        echo json_encode(['success' => false, 'message' => 'No se pudo guardar la autorización.']);
    }

} catch (PDOException $e) {
    // Manejo de errores de base de datos
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}

?>