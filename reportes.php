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
        .content img {
            width: 200px;
            margin-bottom: 20px;
        }
    </style>    
</head>

<body>
    <div class="login-box">
        <div class="content">
            <img src="img/logo_ceia.png" alt="Logo CEIA"> <!-- Puedes cambiar por el logo real -->
        </div>
        <h2>Reportes del Sistema CEIA</h2>
        <ul>
            <li><a href="reportes/planilla_estudiante.php" target="_blank">📝 Planilla de Inscripción (por estudiante)</a></li>
            <li><a href="reportes/listado_estudiantes.php" target="_blank">📋 Listado de Estudiantes</a></li>
            <li><a href="reportes/listado_profesores.php" target="_blank">📋 Listado de Profesores</a></li>
        </ul>
        <br>
        <a href="dashboard.php" class="boton-link">Volver al Dashboard</a>
    </div>
</body>
</html>