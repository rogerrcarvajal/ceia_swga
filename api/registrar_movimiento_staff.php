<?php
error_reporting(0);
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo'])) {
    $response['message'] = 'Acceso denegado o código no proporcionado.';
    echo json_encode($response);
    exit();
}

try {
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'STF-') !== 0) {
        throw new Exception('El código QR no corresponde a un miembro del Staff.');
    }

    $staff_id = (int) substr($codigo, 4);

    $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    $fecha_actual = $dt->format('Y-m-d');
    $hora_actual = $dt->format('H:i:s');

    $sql_prof = "SELECT p.nombre_completo, p.categoria, pp.posicion 
                 FROM profesores p 
                 LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id AND pp.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1)
                 WHERE p.id = :id";
    $stmt_prof = $conn->prepare($sql_prof);
    $stmt_prof->execute([':id' => $staff_id]);
    $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);

    if (!$profesor) {
        throw new Exception("Miembro del staff no encontrado.");
    }

    $nombre_profesor = $profesor['nombre_completo'];
    $posicion = $profesor['posicion'] ?: $profesor['categoria'];

    $conn->beginTransaction();

    // --- LÓGICA DE REGISTRO POR HORA ---
    $stmt_buscar = $conn->prepare("SELECT id, hora_entrada, hora_salida FROM entrada_salida_staff WHERE profesor_id = :staff_id AND fecha = :fecha");
    $stmt_buscar->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual]);
    $registros_hoy = $stmt_buscar->fetchAll(PDO::FETCH_ASSOC);

    $tipo_movimiento = '';

    if (count($registros_hoy) == 0) {
        // PRIMER REGISTRO DEL DÍA: Debe ser una ENTRADA antes de las 12 PM
        if ($hora_actual >= "12:00:00") {
            throw new Exception("La primera lectura (Entrada) debe realizarse antes de las 12:00 PM.");
        }
        $sql_insert = "INSERT INTO entrada_salida_staff (profesor_id, fecha, hora_entrada) VALUES (:staff_id, :fecha, :hora_entrada)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual, ':hora_entrada' => $hora_actual]);
        $tipo_movimiento = 'Entrada';

    } else if (count($registros_hoy) == 1) {
        $registro = $registros_hoy[0];
        // SEGUNDO REGISTRO DEL DÍA: Debe ser una SALIDA después de las 12 PM
        if ($registro['hora_salida'] !== null) {
            throw new Exception("Ya se registró una entrada y una salida completa para hoy.");
        }
        if ($hora_actual < "12:00:00") {
            throw new Exception("La segunda lectura (Salida) debe realizarse después de las 12:00 PM.");
        }
        $sql_update = "UPDATE entrada_salida_staff SET hora_salida = :hora_salida WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([':hora_salida' => $hora_actual, ':id' => $registro['id']]);
        $tipo_movimiento = 'Salida';

    } else {
        // MÁS DE UN REGISTRO: Ya completó el ciclo de hoy
        throw new Exception("Ya se ha completado el ciclo de entrada y salida para hoy.");
    }

    $conn->commit();

    $response['success'] = true;
    $response['message'] = "✅ {$tipo_movimiento} registrada para {$nombre_profesor}.";
    $response['data'] = [
        'tipo' => 'STF',
        'nombre_completo' => $nombre_profesor,
        'posicion' => $posicion,
        'hora_registrada' => $hora_actual,
        'tipo_movimiento' => $tipo_movimiento
    ];

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);