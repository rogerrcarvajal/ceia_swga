<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

try {
    // Leer JSON recibido
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['codigo']) || empty($data['codigo'])) {
        echo json_encode(["status" => "error", "message" => "Código QR no recibido."]);
        exit();
    }

    // Validar prefijo STE
    $codigo = strtoupper(trim($data['codigo']));
    if (strpos($codigo, 'STE') !== 0) {
        echo json_encode(["status" => "error", "message" => "Código no corresponde a un estudiante."]);
        exit();
    }

    // Remover prefijo y obtener ID numérico
    $estudiante_id = (int) substr($codigo, 3);
    if ($estudiante_id <= 0) {
        echo json_encode(["status" => "error", "message" => "ID de estudiante inválido."]);
        exit();
    }

    // Buscar estudiante
    $stmt = $conn->prepare("SELECT id, nombre || ' ' || apellido AS nombre_completo, grado_id 
                            FROM estudiantes 
                            WHERE id = :id");
    $stmt->execute([':id' => $estudiante_id]);
    $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        echo json_encode(["status" => "error", "message" => "Estudiante no encontrado."]);
        exit();
    }

    // Determinar hora actual
    date_default_timezone_set('America/Caracas');
    $fecha = date("Y-m-d");
    $hora_actual = date("H:i:s");

    // Verificar si ya tiene registro de hoy
    $stmt = $conn->prepare("SELECT id, hora_entrada, hora_salida 
                            FROM entrada_salida_estudiantes 
                            WHERE estudiante_id = :id AND fecha = :fecha
                            LIMIT 1");
    $stmt->execute([':id' => $estudiante_id, ':fecha' => $fecha]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        // Si ya tiene entrada pero no salida → marcar salida
        if (empty($registro['hora_salida'])) {
            $upd = $conn->prepare("UPDATE entrada_salida_estudiantes 
                                   SET hora_salida = :hora 
                                   WHERE id = :id");
            $upd->execute([':hora' => $hora_actual, ':id' => $registro['id']]);

            echo json_encode([
                "status" => "exito",
                "tipo" => "estudiante",
                "mensaje" => "Salida registrada.",
                "nombre_completo" => $estudiante['nombre_completo'],
                "grado" => $estudiante['grado_id'],
                "hora_llegada" => $hora_actual
            ]);
            exit();
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Este estudiante ya registró entrada y salida hoy."
            ]);
            exit();
        }
    }

    // Si no tiene registro hoy → registrar entrada
    $ins = $conn->prepare("INSERT INTO entrada_salida_estudiantes 
                            (estudiante_id, fecha, hora_entrada) 
                            VALUES (:id, :fecha, :hora)");
    $ins->execute([
        ':id' => $estudiante_id,
        ':fecha' => $fecha,
        ':hora' => $hora_actual
    ]);

    // Determinar si llegó tarde
    $es_tarde = ($hora_actual > "07:30:00");
    $mensaje = $es_tarde ? "Llegada tarde registrada." : "Llegada puntual registrada.";

    echo json_encode([
        "status" => "exito",
        "tipo" => "estudiante",
        "mensaje" => $mensaje,
        "nombre_completo" => $estudiante['nombre_completo'],
        "grado" => $estudiante['grado_id'],
        "hora_llegada" => $hora_actual,
        "es_tarde" => $es_tarde
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en el servidor: " . $e->getMessage()]);
}