<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
<<<<<<< HEAD
    // Se espera recibir el ID del padre directamente.
    $padre_id = $_GET['id'] ?? null;
    if (!$padre_id) {
        throw new InvalidArgumentException('ID de padre no proporcionado.');
    }

    // Obtener los datos del padre usando el ID proporcionado.
=======
    $estudiante_id = $_GET['estudiante_id'] ?? null;
    if (!$estudiante_id) {
        throw new InvalidArgumentException('ID de estudiante no proporcionado.');
    }

    // 1. Obtener el padre_id desde la tabla estudiantes
    $stmt_est = $conn->prepare("SELECT padre_id FROM estudiantes WHERE id = :estudiante_id");
    $stmt_est->execute([':estudiante_id' => $estudiante_id]);
    $result = $stmt_est->fetch(PDO::FETCH_ASSOC);

    if (!$result || empty($result['padre_id'])) {
        echo json_encode(null); // No hay padre asociado o no se encontró el estudiante
        exit;
    }

    $padre_id = $result['padre_id'];

    // 2. Obtener los datos del padre usando el padre_id
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
    $stmt_padre = $conn->prepare("SELECT * FROM padres WHERE padre_id = :id_padre");
    $stmt_padre->execute([':id_padre' => $padre_id]);
    $padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);

    if (!$padre) {
<<<<<<< HEAD
        // Si no se encuentra, devolver null para que el frontend lo maneje.
        echo json_encode(null);
=======
        echo json_encode(null); // El padre_id existe en estudiantes pero no en la tabla padres (integridad de datos)
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
        exit;
    }

    echo json_encode($padre);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>