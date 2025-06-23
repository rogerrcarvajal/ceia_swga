<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CEIA - Sistema de Gestión Académica</title>
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

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
        }

        .navbar .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(0, 0, 0, 0.9);
            min-width: 160px;
            z-index: 1;
        }

        .dropdown-content a {
            padding: 12px 16px;
            display: block;
        }

        .dropdown:hover .dropdown-content {
            display: block;
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
            font-size: 48px;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 18px;
        }

        .boton-accion {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0057A0;
            color: white;
            border-radius: 5px;
            margin-top: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .boton-accion:hover {
            background-color: #003d73;
        }

        .logout {
            background-color: red;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .logout:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA"> <!-- Puedes cambiar por el logo real -->
        <h1>Bienvenidos <br>Sistema Web de Gestión Académica</h1></br>
        <h2>Centro Educativo Internacional Anzoátegui</h2>
        <p>Selecciona una opción en el menú para comenzar.</p>
    </div>
</body>
</html>