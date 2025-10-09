<?php
// api/guardar_autorizacion_staff.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

// --- DEBUG: Verificar conexión a la BD ---
if (!isset($conn) || !$conn instanceof PDO) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Error crítico: La conexión con la base de datos no se pudo establecer.']);
    exit();
}

$response = ['status' => 'error', 'mensaje' => 'Petición inválida.'];

// --- Validación de Sesión y Rol ---
if (!isset($_SESSION['usuario']['id']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    $response['mensaje'] = 'Acceso denegado. Se requiere autenticación y permisos adecuados.';
    echo json_encode($response);
    exit();
}

// --- Validación de Método y Datos ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y validar datos
    $profesor_id = filter_input(INPUT_POST, 'profesor_id', FILTER_VALIDATE_INT);
    $periodo_id = filter_input(INPUT_POST, 'periodo_id', FILTER_VALIDATE_INT);
    $fecha_permiso = $_POST['fecha_permiso'] ?? null;
    $hora_salida = $_POST['hora_salida'] ?? null;
    $duracion_horas = filter_input(INPUT_POST, 'duracion_horas', FILTER_VALIDATE_FLOAT);
    $motivo = filter_input(INPUT_POST, 'motivo', FILTER_SANITIZE_STRING);
    $registrado_por_usuario_id = filter_input(INPUT_POST, 'registrado_por_usuario_id', FILTER_VALIDATE_INT);

    if (!$profesor_id || !$periodo_id || !$fecha_permiso || !$hora_salida || !$duracion_horas || !$registrado_por_usuario_id) {
        $response['mensaje'] = 'Datos incompletos. Por favor, complete todos los campos requeridos.';
        echo json_encode($response);
        exit();
    }

    // --- Inserción en la Base de Datos ---
    try {
        $sql = "INSERT INTO autorizaciones_salida_staff 
                    (profesor_id, periodo_id, fecha_permiso, hora_salida, duracion_horas, motivo, registrado_por_usuario_id)
                VALUES 
                    (:profesor_id, :periodo_id, :fecha_permiso, :hora_salida, :duracion_horas, :motivo, :registrado_por_usuario_id)";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            ':profesor_id' => $profesor_id,
            ':periodo_id' => $periodo_id,
            ':fecha_permiso' => $fecha_permiso,
            ':hora_salida' => $hora_salida,
            ':duracion_horas' => $duracion_horas,
            ':motivo' => $motivo,
            ':registrado_por_usuario_id' => $registrado_por_usuario_id
        ]);

        if ($stmt->rowCount() > 0) {
            $lastId = $conn->lastInsertId();
            $response = [
                'status' => 'exito',
                'mensaje' => 'Autorización de salida para el personal guardada correctamente.',
                'id' => $lastId // Devolver el ID del nuevo registro
            ];
        } else {
            $response['mensaje'] = 'No se pudo guardar la autorización. No se afectaron filas.';
        }

    } catch (Exception $e) {
        $response['mensaje'] = 'Error al guardar en la base de datos: ' . $e->getMessage();
    }

} else {
    $response['mensaje'] = 'Método de petición no soportado.';
}

echo json_encode($response);
?>