<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
$acceso_stmt = $conn->query("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");

$usuario_rol = $acceso_stmt;

if ($_SESSION['usuario']['rol'] !== 'admin') {
    if ($_SESSION !== $usuario_rol) {
        $_SESSION['error_acceso'] = "Acceso denegado. No tiene permiso para ver esta página.";
        // Aquí puedes redirigir o cargar la ventana modal según tu lógica
    }
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
$periodo_stmt = $conn->query(query: "SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1");

if ($periodo_stmt->rowCount() === 0) {
    // Si no hay período activo, se guarda un mensaje de error en la sesión.
    // La ventana modal se encargará de mostrarlo.
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar o crear uno en el menú de Mantenimiento para poder continuar.";
}

// Obtener lista de estudiantes
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes del Sistema - CEIA</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("/public/img/fondo.jpg");
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
            color: white;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 50%; /* Aumentado para mejor visualización */
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        }

        .content {
            text-align: center;
            margin-top: 30px;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
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
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Reportes del Sistema</h1>
    </div>

    <div class="formulario-contenedor">
        <div class="content">
        
            <ul class="lista-reportes">
                <li>
                    <a href="Planilla_Inscripcion_reporte.php" target="_blank">
                        <span class="icono-reporte">📋</span> Planilla de Inscripción
                    </a>
                    <p>Muestra todos los estudiantes del período escolar activo, indicando si pertenecen al personal (staff).</p>
                </li>

                <li>
                    <a href= "reportes/roster_actual.php" target="_blank">
                        <span class="icono-reporte">📄</span> Roster Actualizado
                    </a>
                    <p>Vista previa del personal admininstrativo y docente, además un listado de estudiantes por grado, con opciones para exportar a PDF.</p>
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