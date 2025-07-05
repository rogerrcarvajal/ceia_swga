<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /../public/index.php");
    exit();
}

// Verificar permisos de usuario
//if ($_SESSION['usuario']['rol'] !== 'admin') {
//    header("Location: /../public/index.php");
//    exit();
//}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

$mensaje = "";

// Agregar usuario sin encriptar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $username = $_POST["username"];
    $clave = $_POST["clave"];
    $rol = $_POST["rol"];

    $check = $conn->prepare("SELECT id FROM usuarios WHERE username = :username");
    $check->execute([':username' => $username]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Ya existe un usuario con ese nombre.";
    } else {
        $sql = "INSERT INTO usuarios (username, password, rol) VALUES (:username, :password, :rol)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $clave, // Guardamos sin encriptar
            ':rol' => $rol
        ]);
        $mensaje = "✅ Usuario creado correctamente.";
    }
}

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="/public/css/estilo_planilla.css">
    <style>
         body {
            margin: 0;
            padding: 0;
            background-image: url("/public/img/fondo.jpg");
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        
        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 0px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 50%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        .form-seccion {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        .content {
            text-align: center;
            margin-top: 50px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
            margin-bottom: 20px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }
    </style>    
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Registro de Usuarios del Sistema</h1></br>
    </div>
    
    <div class="formulario-contenedor">
        <div class="form-seccion">
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST">
            <h3>Crear Usuario</h3>
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <select name="rol" required>
                <option value="">Seleccione Rol</option>
                <option value="admin">Administrador</option>
                <option value="consulta">Consulta</option>
            </select>
            <br><br>
            <button type="submit" name="agregar">Agregar Usuario</button>
            <br></br>
            <a href="dashboard.php" class="boton-link">Volver al Inicio</a></br>
        </form>
    </div>
        <div class="form-seccion">
            <h3>Usuarios Registrados</h3>
            <ul>
                <?php foreach ($usuarios as $u): ?>
                    <li>
                        <?= htmlspecialchars($u['username']) ?> - Rol: <?= $u['rol'] ?>
                        <?php if ($u['rol'] !== 'admin'): ?>
                            <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>