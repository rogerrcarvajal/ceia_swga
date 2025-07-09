<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: /pages/dashboard.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificación segura y funcional
    if ($usuario && password_verify($password, $usuario['password'])) {
        // Guardamos todo el array de usuario en la sesión
        $_SESSION['usuario'] = $usuario;
        
        header("Location: /pages/dashboard.php");
        exit();
    } else {
        $mensaje = "⚠️ Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CEIA SWGA - Login</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("/public/img/fondo.jpg");
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter:blur(10px);
            box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
            border:2px solid rgba(255,255,255,0.18);
            margin: 20px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 30%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .content {
            text-align: center;
            margin-top: 0px;
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
    <br></br>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Sistema Web de<br>Gestión Académica<br></h1><h4>Introzduca su usuario y contraseña</h4></br>
        <?php if ($mensaje): ?>
            <p class="alerta-login"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" id="password" required>
            <input type="checkbox" id="show-password" onclick="password.type = this.checked ? 'text' : 'password'">
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>