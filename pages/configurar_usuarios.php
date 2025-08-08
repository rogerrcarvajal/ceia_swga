<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
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
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// Lógica para agregar un nuevo usuario (ahora guarda la contraseña encriptada)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $profesor_id = $_POST["profesor_id"] ?? null;
    $username = $_POST["username"] ?? '';
    $clave = $_POST["password"] ?? ''; // <-- Cambiado aquí
    $rol = $_POST["rol"] ?? '';
    
    // Encriptar la contraseña antes de guardarla
    $hashed_password = password_hash($clave, PASSWORD_DEFAULT);

    $check = $conn->prepare(query: "SELECT id FROM usuarios WHERE username = :username");
    $check->execute(params: [':username' => $username]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Ya existe un usuario con ese nombre.";
    } else {
        $sql = "INSERT INTO usuarios (username, password, rol, profesor_id) VALUES (:username, :password, :rol, :profesor_id)";
        $stmt = $conn->prepare(query: $sql);
        $stmt->execute(params: [
            ':username' => $username,
            ':password' => $hashed_password, // Se guarda el hash
            ':rol' => $rol,
            ':profesor_id' => ($profesor_id === '') ? null : $profesor_id
        ]);
        $mensaje = "✅ Usuario creado correctamente.";
    }
}

$usuarios = $conn->query(query: "SELECT u.id, u.username, u.rol, p.nombre_completo FROM usuarios u LEFT JOIN profesores p ON u.profesor_id = p.id ORDER BY u.id")->fetchAll(PDO::FETCH_ASSOC);
$profesores_sin_usuario = $conn->query(query: "SELECT id, nombre_completo FROM profesores WHERE id NOT IN (SELECT profesor_id FROM usuarios WHERE profesor_id IS NOT NULL)")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestión de Usuarios</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solidrgb(42, 42, 42); padding-bottom: 10px; }
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
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Usuarios del Sistema</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
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
                        <option value="<?= $prof['id'] ?>"><?= htmlspecialchars(string: $prof['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Nombre de Usuario:</label>
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                
                <label>Contraseña:</label>
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="checkbox" id="show-password" onclick="password.type = this.checked ? 'text' : 'password'">
                
                <label>Rol:</label>
                <select name="rol" required>
                    <option value="">Seleccione Rol</option>
                    <option value="master">Usuario Master</option>
                    <option value="admin">Usuario Administrador</option>
                    <option value="consulta">Usuario Consulta</option>
                </select>
                <br><br>
                <button type="submit" name="agregar">Agregar Usuario</button>
                <!-- Botón para volver al menu Mantto -->
                <a href="/ceia_swga/pages/menu_mantto.php" class="btn">Volver</a> 

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
                                <strong><?= htmlspecialchars(string: $u['username']) ?></strong> (Rol: <?= $u['rol'] ?>)<br>
                                <small><?= $u['nombre_completo'] ? 'Vinculado a: ' . htmlspecialchars(string: $u['nombre_completo']) : 'No vinculado' ?></small>
                            </span>
                            <div>
                                <?php if ($u['username'] !== $_SESSION['usuario']['username']): ?>
                                    <a href="/ceia_swga/pages/editar_usuarios.php?id=<?= $u['id'] ?>">Editar</a> |
                                    <a href="/ceia_swga/pages/eliminar_usuarios.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
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