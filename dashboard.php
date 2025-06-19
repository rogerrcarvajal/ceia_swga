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
    <title>CEIA - Sistema Web de Gestión Académica</title>
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
            background-color: rgba(0, 87, 160, 0.95);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
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

        .dropdown-content a:hover {
            background-color: rgba(0, 0, 0, 0.3);
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
                    <a href="estudiantes.php">Estudiantes</a>
                </div>
            </div>
            <a href="latepass.php">⏱️ Late-Pass</a>
            <a href="reportes.php">📊 Reportes</a>
        </div>
        <div>
            <a href="logout.php" class="logout">Salir</a>
        </div>
    </div>

    <div class="content">
        <h1>Bienvenido al Sistema Web de Gestión Académica - CEIA</h1>
        <p>Selecciona una opción en el menú para comenzar.</p>
    </div>
</body>
</html>