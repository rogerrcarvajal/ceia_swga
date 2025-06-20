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
    <title>Sistema Web de Gestión Académica</title>
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
            background-color: rgba(0, 0, 0, 0.6);
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 60px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
        }

        .navbar a:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }

        .navbar .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(0, 0, 0, 0.9);
            min-width: 200px;
            z-index: 1;
        }

        .dropdown-content a {
            padding: 8px 15px;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .content {
            text-align: center;
            margin-top: 0px;
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
                    <a href="consultar_estudiante.php">Consultar</a>
                    <a href="modificar_estudiante.php">Modificar</a>
                    <a href="eliminar_estudiante.php">Eliminar</a>
                </div>
            </div>
            <a href="latepass.php">⏱️ Late-Pass</a>
            <div class="dropdown">
                <a href="#">📊 Reportes</a>
                <div class="dropdown-content">
                    <a href="reportes/listado_estudiantes.php">Listado de Estudiantes</a>
                    <a href="reportes/listado_profesores.php">Listado de Profesores</a>
                    <a href="reportes/planilla_estudiante.php">Planilla de Estudiantes</a>
                </div>
            </div>
            <a href="mantenimiento.php">📊 Mantenimiento</a>
        </div>
        <div>
            <a href="logout.php" class="logout">Salir</a>
        </div>
    </div>

    <div class="content">
        <h1>Bienvenidos <br>Sistema Web de Gestión Académica</h1></br>
        <h2>Centro Educativo Internacional Anzoátegui</h2>
        <p>Selecciona una opción en el menú para comenzar.</p>
    </div>
</body>
</html>