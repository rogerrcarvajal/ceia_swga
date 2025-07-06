<?php
session_start();
// Solo los administradores pueden eliminar usuarios
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: /pages/dashboard.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

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