<?php
session_start();
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

// Medidas de seguridad mejoradas para incluir rol 'master'
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit();
}

$response = ['status' => 'error', 'message' => 'Datos incompletos.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periodo_id = $_POST['periodo_id'] ?? null;
    $estudiante_id = $_POST['estudiante_id'] ?? null;
    $grado_cursado = $_POST['grado_cursado'] ?? null;

    if ($periodo_id && $estudiante_id && $grado_cursado) {
        try {
            // 1. Verificar si ya existe una asignación para este estudiante en este período.
            $stmt_check = $conn->prepare("SELECT id FROM estudiante_periodo WHERE estudiante_id = :estudiante_id AND periodo_id = :periodo_id");
            $stmt_check->execute([':estudiante_id' => $estudiante_id, ':periodo_id' => $periodo_id]);
            $asignacion_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($asignacion_existente) {
                // 2. Si existe, es una ACTUALIZACIÓN (para casos de grado nulo o para cambiarlo).
                $sql = "UPDATE estudiante_periodo SET grado_cursado = :grado_cursado WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':grado_cursado' => $grado_cursado,
                    ':id' => $asignacion_existente['id']
                ]);
                $response['message'] = 'Grado del estudiante actualizado exitosamente.';

            } else {
                // 3. Si no existe, es una INSERCIÓN nueva.
                $sql = "INSERT INTO estudiante_periodo (estudiante_id, periodo_id, grado_cursado) 
                        VALUES (:estudiante_id, :periodo_id, :grado_cursado)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':estudiante_id' => $estudiante_id,
                    ':periodo_id' => $periodo_id,
                    ':grado_cursado' => $grado_cursado
                ]);
                $response['message'] = 'Estudiante asignado al período exitosamente.';
            }

            $response['status'] = 'exito';

        } catch (PDOException $e) {
            http_response_code(500);
            $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

echo json_encode($response);
?>