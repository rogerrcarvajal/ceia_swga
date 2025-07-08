<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /../public/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CEIA - Sistema de Gestión Académica</title>
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

        .content {
            text-align: center;
            margin-top: 100px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 200px;
            margin-bottom: 20px;
        }

        .content h1 {
            font-size: 50px;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
<img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Bienvenidos <br>Sistema Web de Gestión Académica</h1></br>
        <h2>Centro Educativo Internacional Anzoátegui</h2>
        <p>Powered by R.R.C - @Copyright TM 2025.</p>
    </div>
</body>
</html>