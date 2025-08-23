<?php
error_reporting(0);
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.', 'data' => null];

if (!isset($_SESSION['usuario'])) {
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
    $conn->beginTransaction();
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'VEH-') !== 0) {
        $response['message'] = 'El código QR no corresponde a un vehículo.';
        echo json_encode($response);
        exit();
    }

    $vehiculo_id = (int) substr($codigo, 4);

    if (isset($_POST['timestamp'])) {
        $dt = new DateTime($_POST['timestamp']);
        $dt->setTimezone(new DateTimeZone('America/Caracas'));
    } else {
        $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    }
    $fecha_actual = $dt->format('Y-m-d');
    $hora_actual = $dt->format('H:i:s');
    $registrado_por = $_SESSION['usuario']['nombre'];

    $sql_veh = "SELECT v.placa, v.modelo, e.nombre_completo, e.apellido_completo, ep.grado_cursado 
                FROM vehiculos v 
                JOIN estudiantes e ON v.estudiante_id = e.id 
                JOIN estudiante_periodo ep ON e.id = ep.estudiante_id 
                WHERE v.id = :id AND ep.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1)";
    $stmt_veh = $conn->prepare($sql_veh);
    $stmt_veh->execute([':id' => $vehiculo_id]);
    $vehiculo_info = $stmt_veh->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo_info) {
        throw new Exception("Vehículo no encontrado o no asociado a un estudiante en el período activo.");
    }

    $nombre_estudiante = $vehiculo_info['nombre_completo'] . ' ' . $vehiculo_info['apellido_completo'];

    $sql_buscar = "SELECT id FROM registro_vehiculos WHERE vehiculo_id = :id AND fecha = :fecha AND hora_salida IS NULL";
    $stmt_buscar = $conn->prepare($sql_buscar);
    $stmt_buscar->execute([':id' => $vehiculo_id, ':fecha' => $fecha_actual]);
    $registro_abierto = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

    $tipo_movimiento = '';
    if ($registro_abierto) {
        $sql_update = "UPDATE registro_vehiculos SET hora_salida = :hora_salida, registrado_por = :user WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([':hora_salida' => $hora_actual, ':user' => $registrado_por, ':id' => $registro_abierto['id']]);
        $tipo_movimiento = 'Salida';
    } else {
        $sql_insert = "INSERT INTO registro_vehiculos (vehiculo_id, fecha, hora_entrada, registrado_por) VALUES (:id, :fecha, :hora, :user)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([':id' => $vehiculo_id, ':fecha' => $fecha_actual, ':hora' => $hora_actual, ':user' => $registrado_por]);
        $tipo_movimiento = 'Entrada';
    }

    $conn->commit();

    $response['success'] = true;
    $response['message'] = "✅ {$tipo_movimiento} registrada para vehículo {$vehiculo_info['placa']}.";
    $response['data'] = [
        'tipo' => 'VEH',
        'nombre_completo' => $nombre_estudiante,
        'grado' => $vehiculo_info['grado_cursado'],
        'placa' => $vehiculo_info['placa'],
        'modelo' => $vehiculo_info['modelo'],
        'hora_registrada' => $hora_actual,
        'tipo_movimiento' => $tipo_movimiento
    ];

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = 'Error en el servidor: ' . $e->getMessage();
}

echo json_encode($response);