<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/generar_planilla_pdf.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

try {
    $salida_id = $_GET['salida_id'] ?? null;
    if (!$salida_id) {
        throw new Exception('ID de salida no proporcionado.');
    }

    $filePath = generarPlanillaPDF($salida_id, $conn);

    if ($filePath) {
        echo json_encode(['success' => true, 'filePath' => $filePath, 'message' => 'PDF generado correctamente.']);
    } else {
        throw new Exception('No se pudo generar el PDF.');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>