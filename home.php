<?php
session_start();
require_once "conn/conexion.php";

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
        header("Location: dashboard.php");
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
    <title>Login - CEIA</title>
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
        
        .content img {
            width: 180px;
            margin-bottom: 20px;
        }
    </style>    
</head>
<body>
    <div class="login-box">
        <div class="content">
            <img src="img/logo_ceia.png" alt="Logo CEIA"> <!-- Puedes cambiar por el logo real -->
        </div>
        <h2>Acceso al Sistema Web de Gestión Académica</h2>
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