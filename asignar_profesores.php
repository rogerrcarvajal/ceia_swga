<?php
require_once "../conn/conexion.php";

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
            $stmt->execute([
                ':profesor_id' => $profesor_id,
                ':periodo_id' => $periodo_id,
                ':posicion' => $posicion,
                ':homeroom_teacher' => ($homeroom === 'N/A' || empty($homeroom)) ? null : $homeroom
            ]);
            $response = ['status' => 'success', 'message' => 'Profesor asignado correctamente.'];
        } catch (PDOException $e) {
            $response['message'] = 'Error al asignar: Es posible que el profesor ya esté asignado a este período.';
        }
    } else {
        $response['message'] = 'Faltan datos para realizar la asignación.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
