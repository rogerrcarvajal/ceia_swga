<?php
session_start();
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

// Medidas de seguridad
<<<<<<< HEAD
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
=======
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
>>>>>>> 85c59c242e1db61a1192d67acb07197833c6eeec
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
            // Se inserta la nueva relación en la tabla pivote
            $sql = "INSERT INTO estudiante_periodo (estudiante_id, periodo_id, grado_cursado) 
                    VALUES (:estudiante_id, :periodo_id, :grado_cursado)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':estudiante_id' => $estudiante_id,
                ':periodo_id' => $periodo_id,
                ':grado_cursado' => $grado_cursado
            ]);

            $response['status'] = 'exito';
            $response['message'] = 'Estudiante asignado al período exitosamente.';

        } catch (PDOException $e) {
            // Manejar error de duplicado (código 23505 en PostgreSQL para 'unique constraint')
            if ($e->getCode() == '23505') {
                $response['message'] = 'Este estudiante ya está asignado a este período.';
            } else {
                $response['message'] = 'Error de base de datos: ' . $e->getMessage();
            }
        }
    }
}

echo json_encode($response);
?>