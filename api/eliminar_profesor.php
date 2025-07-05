<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /../public/index.php");
    exit();
}

// Verificar permisos de usuario
//if ($_SESSION['usuario']['rol'] !== 'admin') {
//    header("Location: /../public/index.php");
//    exit();
//}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Verificar si se ha enviado el ID del profesor a eliminar
// Si no se envía, redirigir a la página principal de profesores    

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
header("Location: /../pages/profesores.php");
exit();
?>
