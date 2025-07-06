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

// Inicializar variables
$mensaje = "";
$usuario_a_editar = null;
$usuario_id = $_GET['id'] ?? null;


// Lógica para actualizar el usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $nueva_clave = $_POST['nueva_clave'] ?? '';

    try {
        if (!empty($nueva_clave)) {
            // Si se proporcionó una nueva contraseña, la hasheamos y la actualizamos
            $hashed_password = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET username = :username, rol = :rol, password = :password WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':rol' => $rol,
                ':password' => $hashed_password,
                ':id' => $usuario_id
            ]);
        } else {
            // Si no se proporcionó una nueva contraseña, actualizamos solo el resto
            $sql = "UPDATE usuarios SET username = :username, rol = :rol WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':rol' => $rol,
                ':id' => $usuario_id
            ]);
        }
        $mensaje = "✅ Datos del usuario actualizados correctamente.";
    } catch (PDOException $e) {
        $mensaje = "⚠️ Error al actualizar. Es posible que el nombre de usuario ya exista.";
    }
}

// Obtener los datos actuales del usuario para mostrarlos en el formulario
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $usuario_id]);
$usuario_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario_a_editar) {
    header("Location: /pages/usuarios_configurar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Editar Usuario del Sistema</h1>
    </div>

    <div class="formulario-contenedor" style="max-width: 500px;">
        <div class="form-seccion" style="width: 100%;">
            <h3>Editando a: <?= htmlspecialchars($usuario_a_editar['username']) ?></h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
            <form method="POST">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($usuario_a_editar['username']) ?>" required>

                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="admin" <?= $usuario_a_editar['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="consulta" <?= $usuario_a_editar['rol'] === 'consulta' ? 'selected' : '' ?>>Consulta</option>
                </select>

                <label for="nueva_clave">Nueva Contraseña (opcional):</label>
                <input type="password" id="nueva_clave" name="nueva_clave" placeholder="Dejar en blanco para no cambiar">
                
                <br><br>
                <button type="submit">Actualizar Usuario</button>
                <a href="/pages/usuarios_configurar.php" class="boton-link" style="margin-left: 15px;">Volver</a>
            </form>
        </div>
    </div>
</body>
</html>