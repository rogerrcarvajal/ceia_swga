<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar vehículos.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar vehículos."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// Verificar si se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje_vehiculo'] = "⚠️ ID de vehículo no válido.";
    header("Location: /ceia_swga/pages/registro_vehiculos.php");
    exit;
}

$vehiculo_id = $_GET['id'];

try {
    // Eliminar el vehículo
    $stmt = $conn->prepare("DELETE FROM vehiculos WHERE id = :id");
    $stmt->execute([':id' => $vehiculo_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['mensaje_vehiculo'] = "✅ Vehículo eliminado correctamente.";
    } else {
        $_SESSION['mensaje_vehiculo'] = "⚠️ No se encontró el vehículo para eliminar o ya fue eliminado.";
    }
} catch (PDOException $e) {
    $_SESSION['mensaje_vehiculo'] = "❌ Error al eliminar el vehículo.";
}

// Redirigir de vuelta a la página de registro de vehículos
header("Location: /ceia_swga/pages/registro_vehiculos.php");
exit;