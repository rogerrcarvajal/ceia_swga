<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Obtener período escolar activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear y activar uno.");
}

$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes del Sistema - CEIA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
            color: white;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.75);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 50%; /* Aumentado para mejor visualización */
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        }

        .content {
            text-align: center;
            margin-top: 10px;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
            margin-bottom: 20px;
        }
        
        .content h2 {
            margin-bottom: 25px;
        }

        /* Estilos para la lista de reportes */
        .lista-reportes {
            list-style: none;
            padding: 0;
            text-align: left;
        }

        .lista-reportes li {
            background-color: rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .lista-reportes li:hover {
            background-color: rgba(255, 255, 255, 0.25);
        }

        .lista-reportes a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 1.1em;
        }

        .lista-reportes p {
            margin: 0;
            padding: 0 15px 15px 15px;
            font-size: 0.9em;
            color: #ccc;
        }
        
        .lista-reportes .icono-reporte {
            margin-right: 12px;
            font-size: 1.2em;
        }

    </style>    
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Reportes del Sistema</h1></br>
    </div>

    <div class="formulario-contenedor">
        <div class="content">
        
            <ul class="lista-reportes">
                <li>
                    <a href= "reportes/roster_actual.php" target="_blank">
                        <span class="icono-reporte">📄</span> Roster Actualizado
                    </a>
                    <p>Vista previa del personal y listado de estudiantes por grado, con opciones para exportar a PDF y Excel.</p>
                </li>

                <li>
                    <a href="lista_general_estudiantes.php" target="_blank">
                        <span class="icono-reporte">📋</span> Listado General de Estudiantes
                    </a>
                    <p>Muestra todos los estudiantes del período escolar activo, indicando si pertenecen al personal (staff).</p>
                </li>

                <li>
                    <a href="exportar_codigoqr.php" target="_blank">
                        <span class="icono-reporte">📷</span> Generador de Códigos QR
                    </a>
                    <p>Formulario para generar y exportar códigos QR para el control de acceso de estudiantes, profesores y vehículos.</p>
                </li>
                
                </ul>

            <br>
            <a href="dashboard.php" class="boton-link">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>