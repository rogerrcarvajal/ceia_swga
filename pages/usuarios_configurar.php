<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php");
    exit();
}

// --- CORRECCIÓN APLICADA AQUÍ ---
// Se accede al array 'usuario' y luego a la clave 'rol'
if ($_SESSION['usuario']['rol'] !== 'admin') {
    $_SESSION['error_mensaje'] = "Acceso denegado. No tiene permiso para ver esta página.";
    header("Location: /pages/dashboard.php"); 
    exit();
}

require_once __DIR__ . '/../src/config.php';
$mensaje = "";

// Lógica para agregar un nuevo usuario (con contraseña hasheada)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $profesor_id = $_POST["profesor_id"] ?? null;
    $username = $_POST["username"];
    $clave = $_POST["clave"];
    $rol = $_POST["rol"];
    
    $hashed_password = password_hash($clave, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM usuarios WHERE username = :username");
    $check->execute([':username' => $username]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Ya existe un usuario con ese nombre.";
    } else {
        $sql = "INSERT INTO usuarios (username, password, rol, profesor_id) VALUES (:username, :password, :rol, :profesor_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':rol' => $rol,
            ':profesor_id' => ($profesor_id === '') ? null : $profesor_id
        ]);
        $mensaje = "✅ Usuario creado correctamente.";
    }
}

$usuarios = $conn->query("SELECT u.id, u.username, u.rol, p.nombre_completo FROM usuarios u LEFT JOIN profesores p ON u.profesor_id = p.id ORDER BY u.id")->fetchAll(PDO::FETCH_ASSOC);
$profesores_sin_usuario = $conn->query("SELECT id, nombre_completo FROM profesores WHERE id NOT IN (SELECT profesor_id FROM usuarios WHERE profesor_id IS NOT NULL)")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Usuarios del Sistema</h1>
    </div>
    
    <div class="formulario-contenedor">
        <div class="form-seccion">
            <h3>Gestión de Usuario</h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
            <form method="POST">
                <label>Vincular a Staff/Profesor (Opcional):</label>
                <select name="profesor_id">
                    <option value="">-- No vincular / Usuario genérico --</option>
                    <?php foreach ($profesores_sin_usuario as $prof): ?>
                        <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Nombre de Usuario:</label>
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                
                <label>Contraseña:</label>
                <input type="password" name="clave" placeholder="Contraseña" required>
                
                <label>Rol:</label>
                <select name="rol" required>
                    <option value="">Seleccione Rol</option>
                    <option value="admin">Administrador</option>
                    <option value="consulta">Consulta</option>
                </select>
                <br><br>
                <button type="submit" name="agregar">Agregar Usuario</button>
            </form>
        </div>

        <div class="form-seccion">
            <h3>Usuarios Registrados</h3>
            <ul class="lista-profesores">
                <?php if (empty($usuarios)): ?>
                    <li>No hay usuarios registrados.</li>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <li>
                            <span>
                                <strong><?= htmlspecialchars($u['username']) ?></strong> (Rol: <?= $u['rol'] ?>)<br>
                                <small><?= $u['nombre_completo'] ? 'Vinculado a: ' . htmlspecialchars($u['nombre_completo']) : 'No vinculado' ?></small>
                            </span>
                            <div>
                                <?php if ($u['username'] !== $_SESSION['usuario']['username']): ?>
                                    <a href="/pages/editar_usuario.php?id=<?= $u['id'] ?>">Editar</a> |
                                    <a href="/api/eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>