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

$mensaje = "";

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---

$periodo_stmt = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() === 0) {
    // Si no hay período activo, se guarda un mensaje de error en la sesión.
    // La ventana modal se encargará de mostrarlo.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar o crear uno en el menú de Mantenimiento para poder continuar.";
}

// Lógica para agregar un nuevo usuario (ahora guarda la contraseña encriptada)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $profesor_id = $_POST["profesor_id"] ?? null;
    $username = $_POST["username"];
    $clave = $_POST["clave"];
    $rol = $_POST["rol"];
    
    // Encriptar la contraseña antes de guardarla
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
            ':password' => $hashed_password, // Se guarda el hash
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
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url('/public/img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.75); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solid #0057A0; padding-bottom: 10px; }
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
        .content img { width: 150px; }
        .lista-profesores { list-style: none; padding: 0; max-height: 400px; overflow-y: auto; }
        .lista-profesores li { background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .lista-profesores a { color: #87cefa; text-decoration: none; margin-left: 10px; }
    </style>    
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Usuarios del Sistema</h1></br>
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
                                    <a href="/pages/editar_usuarios.php?id=<?= $u['id'] ?>">Editar</a> |
                                    <a href="/pages/eliminar_usuarios.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
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