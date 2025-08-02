<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$input = json_decode(file_get_contents('php://input'), true);
$profesor_id = filter_var($input['profesor_id'] ?? 0, FILTER_VALIDATE_INT);

// Preparamos una respuesta por defecto en caso de error inicial
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if (!$estudiante_id) {
    echo json_encode($response);
    exit();
}

try {
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    // Buscar si ya hay un registro para este profesor hoy
    $stmt = $conn->prepare("SELECT id, hora_entrada FROM entrada_salida_staff WHERE profesor_id = ? AND fecha = ?");
    $stmt->execute([$profesor_id, $fecha]);
    $registro_hoy = $stmt->fetch();

    if ($registro_hoy) {
        // Si ya hay registro, es una SALIDA
        $sql = "UPDATE entrada_salida_staff SET hora_salida = ? WHERE id = ?";
        $conn->prepare($sql)->execute([$hora, $registro_hoy['id']]);
        $mensaje = "Salida registrada.";
    } else {
        // Si no hay registro, es una ENTRADA
        $sql = "INSERT INTO entrada_salida_staff (profesor_id, fecha, hora_entrada) VALUES (?, ?, ?)";
        $conn->prepare($sql)->execute([$profesor_id, $fecha, $hora]);
        $mensaje = "Entrada registrada.";
    }
    
    // ... (Obtener nombre del profesor para la respuesta) ...
    $response = ['status' => 'exito', 'nombre_completo' => $nombre_profesor, 'mensaje' => $mensaje];

} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}
echo json_encode($response);
?>