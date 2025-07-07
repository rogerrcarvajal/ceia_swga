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

// Verificar si se ha enviado el ID del usuario a eliminar
$usuario_id = $_GET['id'] ?? null;

if ($usuario_id) {
    // Medida de seguridad: no permitir que un admin se elimine a sí mismo desde aquí
    if ($usuario_id == $_SESSION['usuario']['id']) {
        // Opcional: guardar un mensaje de error en la sesión
        $_SESSION['error_mensaje'] = "No puedes eliminar tu propia cuenta.";
    } else {
        try {
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $usuario_id]);
        } catch (PDOException $e) {
            // Manejo de errores
            $_SESSION['error_mensaje'] = "Error al eliminar el usuario.";
        }
    }
}

// Redirigir siempre de vuelta a la página de gestión de usuarios
header("Location: /pages/usuarios_configurar.php");
exit();
?>