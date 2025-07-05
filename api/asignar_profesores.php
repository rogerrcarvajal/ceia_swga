<?php
// CORRECCIÓN: La ruta al archivo de conexión debe ser la misma que los otros scripts de backend.
require_once __DIR__ . '/../src/config.php';

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesor_id = $_POST['profesor_id'] ?? null;
    $periodo_id = $_POST['periodo_id'] ?? null;
    $posicion = $_POST['posicion'] ?? null;
    $homeroom = $_POST['homeroom_teacher'] ?? null;

    if ($profesor_id && $periodo_id && $posicion) {
        try {
            $sql = "INSERT INTO profesor_periodo (profesor_id, periodo_id, posicion, homeroom_teacher)
                    VALUES (:profesor_id, :periodo_id, :posicion, :homeroom_teacher)";
            $stmt = $conn->prepare($sql);
            
            // Si el homeroom teacher es 'N/A' o está vacío, se inserta NULL en la base de datos.
            $homeroom_value = ($homeroom === 'N/A' || empty($homeroom)) ? null : $homeroom;

            $stmt->execute([
                ':profesor_id' => $profesor_id,
                ':periodo_id' => $periodo_id,
                ':posicion' => $posicion,
                ':homeroom_teacher' => $homeroom_value
            ]);
            $response = ['status' => 'success', 'message' => 'Profesor asignado correctamente.'];
        } catch (PDOException $e) {
            // Este error se activa si se viola la constraint UNIQUE (profesor_id, periodo_id)
            $response['message'] = 'Error al asignar: Es posible que el profesor ya esté asignado a este período.';
        }
    } else {
        $response['message'] = 'Faltan datos para realizar la asignación.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
