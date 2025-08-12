<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['codigo']) || empty($data['codigo'])) {
        echo json_encode(["status" => "error", "message" => "Código QR no recibido."]);
        exit();
    }

    $codigo = strtoupper(trim($data['codigo']));
    if (strpos($codigo, 'VHI') !== 0) {
        echo json_encode(["status" => "error", "message" => "Código no corresponde a un vehículo."]);
        exit();
    }

    $vehiculo_id = (int) substr($codigo, 3);
    if ($vehiculo_id <= 0) {
        echo json_encode(["status" => "error", "message" => "ID de vehículo inválido."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, placa, modelo 
                            FROM vehiculos 
                            WHERE id = :id");
    $stmt->execute([':id' => $vehiculo_id]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo) {
        echo json_encode(["status" => "error", "message" => "Vehículo no encontrado."]);
        exit();
    }

    date_default_timezone_set('America/Caracas');
    $fecha = date("Y-m-d");
    $hora_actual = date("H:i:s");

    $stmt = $conn->prepare("SELECT id, hora_entrada, hora_salida 
                            FROM registro_vehiculos 
                            WHERE vehiculo_id = :id AND fecha = :fecha
                            LIMIT 1");
    $stmt->execute([':id' => $vehiculo_id, ':fecha' => $fecha]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        if (empty($registro['hora_salida'])) {
            $upd = $conn->prepare("UPDATE registro_vehiculos 
                                   SET hora_salida = :hora 
                                   WHERE id = :id");
            $upd->execute([':hora' => $hora_actual, ':id' => $registro['id']]);

            echo json_encode([
                "status" => "exito",
                "tipo" => "vehiculo",
                "mensaje" => "Salida registrada.",
                "placa" => $vehiculo['placa'],
                "modelo" => $vehiculo['modelo'],
                "hora" => $hora_actual
            ]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Este vehículo ya registró entrada y salida hoy."]);
            exit();
        }
    }

    $ins = $conn->prepare("INSERT INTO registro_vehiculos (vehiculo_id, fecha, hora_entrada) 
                           VALUES (:id, :fecha, :hora)");
    $ins->execute([':id' => $vehiculo_id, ':fecha' => $fecha, ':hora' => $hora_actual]);

    echo json_encode([
        "status" => "exito",
        "tipo" => "vehiculo",
        "mensaje" => "Entrada registrada.",
        "placa" => $vehiculo['placa'],
        "modelo" => $vehiculo['modelo'],
        "hora" => $hora_actual
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en el servidor: " . $e->getMessage()]);
}