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

// Obtener período escolar activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear uno.");
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