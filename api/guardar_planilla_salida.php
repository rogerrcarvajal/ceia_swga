<?php
session_start();
require_once __DIR__ . '/../src/config.php';

header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

// Validar rol de usuario
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

try {
    // Recoger datos del formulario vía POST
    $estudiante_id = $_POST['estudiante_id'] ?? null;
    $fecha_salida = $_POST['fecha_salida'] ?? null;
    $hora_salida = $_POST['hora_salida'] ?? null;
    $motivo = htmlspecialchars($_POST['motivo'] ?? '', ENT_QUOTES, 'UTF-8');
    $registrado_por_usuario_id = $_SESSION['usuario']['id'] ?? null;
    $autorizado_por = $_POST['autorizado_por'] ?? null;

    $retirado_por_nombre = null;
    $retirado_por_parentesco = null;

    if ($autorizado_por === 'padre') {
        $padre_id_form = $_POST['padre_id'] ?? null;
        if (!$padre_id_form) throw new Exception('ID del padre no proporcionado.');

        // Verificación de consistencia: que el padre pertenezca al estudiante
        $stmt_verif = $conn->prepare("SELECT padre_id FROM estudiantes WHERE id = :estudiante_id");
        $stmt_verif->execute([':estudiante_id' => $estudiante_id]);
        $estudiante_data = $stmt_verif->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante_data || $estudiante_data['padre_id'] != $padre_id_form) {
            throw new Exception('El padre seleccionado no corresponde al estudiante.');
        }

        $stmt = $conn->prepare("SELECT padre_nombre, padre_apellido FROM padres WHERE padre_id = :id");
        $stmt->execute(['id' => $padre_id_form]);
        $padre = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$padre) throw new Exception('Padre no encontrado.');
        $retirado_por_nombre = $padre['padre_nombre'] . ' ' . $padre['padre_apellido'];
        $retirado_por_parentesco = 'Padre';
    } elseif ($autorizado_por === 'madre') {
        $madre_id_form = $_POST['madre_id'] ?? null;
        if (!$madre_id_form) throw new Exception('ID de la madre no proporcionado.');

        // Verificación de consistencia: que la madre pertenezca al estudiante
        $stmt_verif = $conn->prepare("SELECT madre_id FROM estudiantes WHERE id = :estudiante_id");
        $stmt_verif->execute([':estudiante_id' => $estudiante_id]);
        $estudiante_data = $stmt_verif->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante_data || $estudiante_data['madre_id'] != $madre_id_form) {
            throw new Exception('La madre seleccionada no corresponde al estudiante.');
        }

        $stmt = $conn->prepare("SELECT madre_nombre, madre_apellido FROM madres WHERE madre_id = :id");
        $stmt->execute(['id' => $madre_id_form]);
        $madre = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$madre) throw new Exception('Madre no encontrada.');
        $retirado_por_nombre = $madre['madre_nombre'] . ' ' . $madre['madre_apellido'];
        $retirado_por_parentesco = 'Madre';
    } elseif ($autorizado_por === 'otro') {
        $retirado_por_nombre = htmlspecialchars($_POST['retirado_por_nombre'] ?? null, ENT_QUOTES, 'UTF-8');
        $retirado_por_parentesco = htmlspecialchars($_POST['retirado_por_parentesco'] ?? null, ENT_QUOTES, 'UTF-8');
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