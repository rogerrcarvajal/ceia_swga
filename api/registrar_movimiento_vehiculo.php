<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.'];

// TODO: Considerar un rol de 'guardia' con permisos específicos para esta operación.
if (!isset($_SESSION['usuario'])) { // Se quitó la restricción de rol por ahora
    $response['message'] = 'Acceso denegado. Se requiere iniciar sesión.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo'])) {
    $response['message'] = 'Acceso denegado o código no proporcionado.';
    echo json_encode($response);
    exit();
}

try {
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'VEH-') !== 0) {
        $response['message'] = 'El código QR no corresponde a un vehículo.';
        echo json_encode($response);
        exit();
    }

        $vehiculo_id = (int) substr($codigo, 4);

    // Usar el timestamp del cliente si está disponible, si no, usar la hora del servidor
    if (isset($_POST['timestamp'])) {
        $dt = new DateTime($_POST['timestamp']);
        $dt->setTimezone(new DateTimeZone('America/Caracas')); // Ajustar a la zona horaria del servidor
    } else {
        $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    }
    $fecha_actual = $dt->format('Y-m-d');
    $hora_actual = $dt->format('H:i:s');

    $registrado_por = $_SESSION['usuario']['nombre']; // Guardar el nombre del usuario que registra

    // Obtener datos del vehículo para el mensaje
    $stmt_veh = $conn->prepare("SELECT placa, modelo FROM vehiculos WHERE id = :id");
    $stmt_veh->execute([':id' => $vehiculo_id]);
    $vehiculo = $stmt_veh->fetch(PDO::FETCH_ASSOC);
    $info_vehiculo = $vehiculo ? "{$vehiculo['placa']} - {$vehiculo['modelo']}" : 'Vehículo Desconocido';

    // Buscar un registro de entrada abierto para hoy
    $sql_buscar = "SELECT id FROM registro_vehiculos WHERE vehiculo_id = :id AND fecha = :fecha AND hora_salida IS NULL";
    $stmt_buscar = $conn->prepare($sql_buscar);
    $stmt_buscar->execute([':id' => $vehiculo_id, ':fecha' => $fecha_actual]);
    $registro_abierto = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

    if ($registro_abierto) {
        // Si hay un registro abierto, se marca la salida
        $sql_update = "UPDATE registro_vehiculos SET hora_salida = :hora_salida, registrado_por = :user WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([':hora_salida' => $hora_actual, ':user' => $registrado_por, ':id' => $registro_abierto['id']]);
        $response['message'] = "✅ Salida registrada: {$info_vehiculo} ({$hora_actual})";
    } else {
        // Si no hay registro abierto, se marca la entrada
        $sql_insert = "INSERT INTO registro_vehiculos (vehiculo_id, fecha, hora_entrada, registrado_por) VALUES (:id, :fecha, :hora, :user)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([':id' => $vehiculo_id, ':fecha' => $fecha_actual, ':hora' => $hora_actual, ':user' => $registrado_por]);
        $response['message'] = "✅ Entrada registrada: {$info_vehiculo} ({$hora_actual})";
    }

    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error en el servidor: ' . $e->getMessage();
}

echo json_encode($response);