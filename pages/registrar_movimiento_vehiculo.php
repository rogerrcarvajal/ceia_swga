<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    exit('Acceso denegado.');
}

require_once __DIR__ . '/../src/config.php';

$vehiculo_id = $_GET['id'] ?? 0;
if (!$vehiculo_id) exit('ID no válido.');

$fecha_actual = date('Y-m-d');
$hora_actual = date('H:i:s');
$usuario = $_SESSION['usuario']['username'];

// Verificar si ya tiene entrada hoy
$stmt_check = $conn->prepare("SELECT id, hora_entrada, hora_salida FROM registro_vehiculos WHERE vehiculo_id = :id AND fecha = :fecha");
$stmt_check->execute([':id' => $vehiculo_id, ':fecha' => $fecha_actual]);
$registro = $stmt_check->fetch(PDO::FETCH_ASSOC);

if ($registro) {
    if (!$registro['hora_salida']) {
        // Registrar salida
        $stmt = $conn->prepare("UPDATE registro_vehiculos SET hora_salida = :hora, observaciones = 'Salida registrada', registrado_por = :usuario WHERE id = :reg_id");
        $stmt->execute([':hora' => $hora_actual, ':usuario' => $usuario, ':reg_id' => $registro['id']]);
        $mensaje = "Salida registrada correctamente.";
    } else {
        $mensaje = "Ya se registró entrada y salida hoy.";
    }
} else {
    // Registrar entrada
    $stmt = $conn->prepare("INSERT INTO registro_vehiculos (vehiculo_id, fecha, hora_entrada, observaciones, registrado_por) VALUES (:id, :fecha, :hora, 'Entrada registrada', :usuario)");
    $stmt->execute([':id' => $vehiculo_id, ':fecha' => $fecha_actual, ':hora' => $hora_actual, ':usuario' => $usuario]);
    $mensaje = "Entrada registrada correctamente.";
}

header("Location: /ceia_swga/pages/gestion_vehiculos.php?msg=" . urlencode($mensaje));
exit();
?>