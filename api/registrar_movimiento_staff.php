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
    if (strpos($codigo, 'STF') !== 0) {
        echo json_encode(["status" => "error", "message" => "Código no corresponde a un miembro del staff."]);
        exit();
    }

    $staff_id = (int) substr($codigo, 3);
    if ($staff_id <= 0) {
        echo json_encode(["status" => "error", "message" => "ID de staff inválido."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, nombre || ' ' || apellido AS nombre_completo 
                            FROM staff 
                            WHERE id = :id");
    $stmt->execute([':id' => $staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$staff) {
        echo json_encode(["status" => "error", "message" => "Miembro del staff no encontrado."]);
        exit();
    }

    date_default_timezone_set('America/Caracas');
    $fecha = date("Y-m-d");
    $hora_actual = date("H:i:s");

    $stmt = $conn->prepare("SELECT id, hora_entrada, hora_salida 
                            FROM entrada_salida_staff 
                            WHERE profesor_id = :id AND fecha = :fecha
                            LIMIT 1");
    $stmt->execute([':id' => $staff_id, ':fecha' => $fecha]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        if (empty($registro['hora_salida'])) {
            $upd = $conn->prepare("UPDATE entrada_salida_staff 
                                   SET hora_salida = :hora 
                                   WHERE id = :id");
            $upd->execute([':hora' => $hora_actual, ':id' => $registro['id']]);

            echo json_encode([
                "status" => "exito",
                "tipo" => "staff",
                "mensaje" => "Salida registrada.",
                "nombre_completo" => $staff['nombre_completo'],
                "hora" => $hora_actual
            ]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Este miembro ya registró entrada y salida hoy."]);
            exit();
        }
    }

    $ins = $conn->prepare("INSERT INTO entrada_salida_staff (profesor_id, fecha, hora_entrada) 
                           VALUES (:id, :fecha, :hora)");
    $ins->execute([':id' => $staff_id, ':fecha' => $fecha, ':hora' => $hora_actual]);

    echo json_encode([
        "status" => "exito",
        "tipo" => "staff",
        "mensaje" => "Entrada registrada.",
        "nombre_completo" => $staff['nombre_completo'],
        "hora" => $hora_actual
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en el servidor: " . $e->getMessage()]);
}