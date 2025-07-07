<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Verificar si el rol del usuario NO es 'admin'
if ($_SESSION['usuario']['rol'] !== 'admin') {
    // Guardar un mensaje de error en la sesión para mostrarlo en el dashboard
    $_SESSION['error_mensaje'] = "Acceso denegado. No tiene permiso para ver esta página.";
    header("Location: /../pages/dashboard.php"); // Redirigir a una página segura
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---

$periodo_stmt = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() === 0) {
    // Si no hay período activo, se guarda un mensaje de error en la sesión.
    // La ventana modal se encargará de mostrarlo.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar o crear uno en el menú de Mantenimiento para poder continuar.";
}

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
