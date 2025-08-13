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
    $conn->beginTransaction();
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'STF-') !== 0) {
        $response['message'] = 'El código QR no corresponde a un miembro del Staff.';
        echo json_encode($response);
        exit();
    }

    $staff_id = (int) substr($codigo, 4);

    if (isset($_POST['timestamp'])) {
        $dt = new DateTime($_POST['timestamp']);
        $dt->setTimezone(new DateTimeZone('America/Caracas'));
    } else {
        $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    }
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

    $sql_buscar = "SELECT id FROM entrada_salida_staff WHERE profesor_id = :staff_id AND fecha = :fecha AND hora_salida IS NULL";
    $stmt_buscar = $conn->prepare($sql_buscar);
    $stmt_buscar->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual]);
    $registro_abierto = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

    $tipo_movimiento = '';
    if ($registro_abierto) {
        $sql_update = "UPDATE entrada_salida_staff SET hora_salida = :hora_salida WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([':hora_salida' => $hora_actual, ':id' => $registro_abierto['id']]);
        $tipo_movimiento = 'Salida';
    } else {
        $sql_insert = "INSERT INTO entrada_salida_staff (profesor_id, fecha, hora_entrada) VALUES (:staff_id, :fecha, :hora_entrada)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual, ':hora_entrada' => $hora_actual]);
        $tipo_movimiento = 'Entrada';
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
    $response['message'] = 'Error en el servidor: ' . $e->getMessage();
}

echo json_encode($response);