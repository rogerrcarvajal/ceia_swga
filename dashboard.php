<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CEIA - Sistema de Gestión</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.4);
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: block;
        }

        .navbar a:hover {
            background-color: rgba(0, 0, 0, 0.2);
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
    <div class="navbar">
        <div>
            <a href="dashboard.php">🏠 Home</a>
            <div class="dropdown">
                <a href="#">📝 Inscripción</a>
                <div class="dropdown-content">
                    <a href="ingresar_estudiante.php">Ingresar</a>
                    // <a href="consultar_estudiante.php">Consultar</a>
                    // <a href="modificar_estudiante.php">Modificar</a>
                    // <a href="eliminar_estudiante.php">Eliminar</a>
                </div>
            </div>
            <a href="latepass.php">⏱️ Late-Pass</a>
            <a href="reportes.php">📊 Reportes</a>
            <a href="mantenimiento.php">📊 Mantenimiento</a>
        </div>
        <div>
            <a href="logout.php" class="logout">Salir</a>
        </div>
    </div>

    <div class="content">
        <h1>Bienvenido al Sistema de Gestión Académica - CEIA</h1>
        <p>Selecciona una opción en el menú para comenzar.</p>
    </div>
</body>
</html>