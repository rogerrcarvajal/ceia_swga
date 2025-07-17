<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");

$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
$periodo_stmt = $conn->query(query: "SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

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
