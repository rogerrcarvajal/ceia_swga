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
    <title>Reportes - CEIA</title>
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
        <h2>Reportes del Sistema</h2>
        <ul>
            <li><a href="reportes/planilla_estudiante.php" target="_blank">📝 Planilla de Inscripción (por estudiante)</a></li>
            <li><a href="reportes/listado_estudiantes.php" target="_blank">📋 Listado de Estudiantes</a></li>
            <li><a href="reportes/listado_profesores.php" target="_blank">📋 Listado de Profesores</a></li>
        </ul>
        <br>
        <a href="dashboard.php" class="boton-link">Volver al Inicio</a>
    </div>
</body>
</html>