<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

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
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 30%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .content {
            text-align: center;
            margin-top: 10px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 150px;
            margin-bottom: 0px;
        }
    </style>    
</head>
<body>
    <?php include 'navbar.php'; ?>
    <br></br>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h2>Registro de Usuarios del Sistema</h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <select name="rol" required>
                <option value="">Seleccione Rol</option>
                <option value="admin">Administrador</option>
                <option value="consulta">Consulta</option>
            </select>
            <br><br>
            <button type="submit" name="agregar">Agregar Usuario</button>
        </form>

        <hr>
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

        <br>
        <a href="dashboard.php" class="boton-link">Volver al Inicio</a>
    </div>
</body>
</html>