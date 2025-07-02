<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$profesor_id = $_GET['id'] ?? null;

if ($profesor_id) {
    try {
        // Gracias a "ON DELETE CASCADE" en la tabla 'profesor_periodo',
        // al eliminar un profesor, se eliminarán automáticamente todas sus asignaciones.
        $sql = "DELETE FROM profesores WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $profesor_id]);
    } catch (PDOException $e) {
        // Podrías guardar el error en una sesión para mostrarlo en la página principal
        // $_SESSION['error_message'] = "Error al eliminar: " . $e->getMessage();
    }
}

// Redirigir siempre de vuelta a la página principal de profesores
header("Location: profesores.php");
exit();
?>