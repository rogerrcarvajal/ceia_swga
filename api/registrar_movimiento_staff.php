<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['success' => false, 'message' => 'Petición inválida.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo'])) {
    $response['message'] = 'Acceso denegado o código no proporcionado.';
    echo json_encode($response);
    exit();
}

try {
    $codigo = strtoupper(trim($_POST['codigo']));
    
    if (strpos($codigo, 'STF-') !== 0) {
        $response['message'] = 'El código QR no corresponde a un miembro del Staff.';
        echo json_encode($response);
        exit();
    }

        $staff_id = (int) substr($codigo, 4);

    // Usar el timestamp del cliente si está disponible, si no, usar la hora del servidor
    if (isset($_POST['timestamp'])) {
        $dt = new DateTime($_POST['timestamp']);
        $dt->setTimezone(new DateTimeZone('America/Caracas')); // Ajustar a la zona horaria del servidor
    } else {
        $dt = new DateTime('now', new DateTimeZone('America/Caracas'));
    }
    $fecha_actual = $dt->format('Y-m-d');
    $hora_actual = $dt->format('H:i:s');

    // Obtener nombre del profesor para el mensaje
    $stmt_prof = $conn->prepare("SELECT nombre_completo FROM profesores WHERE id = :id");
    $stmt_prof->execute([':id' => $staff_id]);
    $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);
    $nombre_profesor = $profesor ? $profesor['nombre_completo'] : 'Desconocido';

    // Buscar un registro de entrada abierto para hoy
    $sql_buscar = "SELECT id FROM entrada_salida_staff WHERE profesor_id = :staff_id AND fecha = :fecha AND hora_salida IS NULL";
    $stmt_buscar = $conn->prepare($sql_buscar);
    $stmt_buscar->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual]);
    $registro_abierto = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

    if ($registro_abierto) {
        // Si hay un registro abierto, se marca la salida
        $sql_update = "UPDATE entrada_salida_staff SET hora_salida = :hora_salida WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([':hora_salida' => $hora_actual, ':id' => $registro_abierto['id']]);
        $response['message'] = "✅ Salida registrada: {$nombre_profesor} ({$hora_actual})";
    } else {
        // Si no hay registro abierto, se marca la entrada
        $sql_insert = "INSERT INTO entrada_salida_staff (profesor_id, fecha, hora_entrada) VALUES (:staff_id, :fecha, :hora_entrada)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([':staff_id' => $staff_id, ':fecha' => $fecha_actual, ':hora_entrada' => $hora_actual]);
        $response['message'] = "✅ Entrada registrada: {$nombre_profesor} ({$hora_actual})";
    }

    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error en el servidor: ' . $e->getMessage();
}

echo json_encode($response);