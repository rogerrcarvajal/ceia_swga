<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = [
    'padre' => null,
    'madre' => null
];

$estudiante_id = $_GET['estudiante_id'] ?? null;

if (!$estudiante_id) {
    echo json_encode($response);
    exit();
}

try {
    // 1. Obtener los IDs de los representantes desde la tabla estudiantes
    $stmt_est = $conn->prepare("SELECT padre_id, madre_id FROM estudiantes WHERE id = :id");
    $stmt_est->execute(['id' => $estudiante_id]);
    $representante_ids = $stmt_est->fetch(PDO::FETCH_ASSOC);

    if ($representante_ids) {
        // 2. Buscar datos del padre si existe el ID
        if (!empty($representante_ids['padre_id'])) {
            $stmt_padre = $conn->prepare("SELECT padre_id as id, nombre_completo FROM padres WHERE padre_id = :id");
            $stmt_padre->execute(['id' => $representante_ids['padre_id']]);
            $response['padre'] = $stmt_padre->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        // 3. Buscar datos de la madre si existe el ID
        if (!empty($representante_ids['madre_id'])) {
            $stmt_madre = $conn->prepare("SELECT madre_id as id, nombre_completo FROM madres WHERE madre_id = :id");
            $stmt_madre->execute(['id' => $representante_ids['madre_id']]);
            $response['madre'] = $stmt_madre->fetch(PDO::FETCH_ASSOC) ?: null;
        }
    }

} catch (PDOException $e) {
    // En un entorno de producción, sería mejor loguear este error
    // y no exponer detalles al cliente.
    $response['error'] = 'Error de base de datos: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
