<?php
session_start();
require_once __DIR__ . '/../src/config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    $sql = "SELECT * FROM usuarios WHERE username = :usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Comparación sin encriptación
    if ($user && $clave === $user['password']) {
        $_SESSION['usuario'] = $user['username'];
        $_SESSION['rol'] = $user['rol'];
        header(header: "Location: /../pages/dashboard.php");
        exit();
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
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
            background-color: rgba(0, 0, 0, 0.7);
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
    <br></br>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Sistema Web de<br>Gestión Académica<br></h1><h4>Introduca su usuario y contraseña</h4></br>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required><br>
            <input type="password" name="clave" placeholder="Contraseña" required><br>
            <button type="submit">Ingresar</button>
        </form>
        <?php if ($mensaje): ?>
            <div class="alerta"><?php echo $mensaje; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>