<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

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
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
        body { margin: 0; padding: 0; background-image: url('/public/img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.75); margin: 30px auto; padding: 30px; border-radius: 10px; max-width: 500px; }
        .select {width: 30%;}
        .content { text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black; }
        .content img { width: 150px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Editar Usuario del Sistema</h1></br>
    </div>

    <div class="formulario-contenedor">
            <h3>Editando a: <?= htmlspecialchars($usuario_a_editar['username']) ?></h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
            <form method="POST">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($usuario_a_editar['username']) ?>" required>

                <br>
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" class="select" required>
                    <option value="admin" <?= $usuario_a_editar['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="consulta" <?= $usuario_a_editar['rol'] === 'consulta' ? 'selected' : '' ?>>Consulta</option>
                </select>

                <br>
                <label for="nueva_clave">Nueva Contraseña (opcional):</label>
                <input type="password" id="nueva_clave" name="nueva_clave" placeholder="Dejar en blanco para no cambiar">
                
                <br><br>
                <button type="submit">Actualizar Usuario</button>
                <a href="/pages/usuarios_configurar.php" class="boton-link" style="margin-left: 15px;">Volver</a>
            </form>       
    </div>
</body>
</html>