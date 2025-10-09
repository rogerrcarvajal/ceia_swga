<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// --- CONTROL DE ACCESO ---
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master', 'admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados tienen acceso a este módulo.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados tienen acceso a este módulo."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Gestión de Staff</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <!-- Incluir Bootstrap CSS para el colapso -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 20px; border-radius: 10px; max-width: 50%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .content { color: white; text-align: center; margin-top: 30px; text-shadow: 1px 1px 2px black;}
        .content img { width: 250px;}
        .content h2 { margin-bottom: 25px;}
        /* Estilos para la lista de menu */
        .lista-menu { backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); list-style: none; padding: 0; text-align: left; width: 100%;}
        .lista-menu li { background-color: rgba(255, 255, 255, 0.1); margin-bottom: 15px; border-radius: 5px; transition: background-color 0.3s ease;}
        .lista-menu li:hover { background-color: rgba(255, 255, 255, 0.25);}
        .lista-menu a { display: block; padding: 15px; color: white; text-decoration: none; font-size: 1.1em;}
        .lista-menu p { margin: 0; padding: 0 15px 15px 15px; font-size: 0.9em; color: #ccc;}
        .lista-menu .icono-reporte { margin-right: 12px; font-size: 1.2em;}
        .card-body a { color: #fff; text-decoration: none; display: block; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .card-body a:hover { background-color: rgba(255,255,255,0.1); }
    </style>    
</head>

<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Staff</h1>
    </div>

    <div class="formulario-contenedor">
        <div class="content" style="width: 100%;">       
            <ul class="lista-menu">
                <li>
                    <a href="/ceia_swga/pages/profesores_registro.php">
                        <span class="icono-reporte">👥</span> Gestión de Personal/Staff
                    </a>
                    <p>Permite registrar, editar y consultar el personal docente y administrativo.</p>
                </li>

                <li>
                    <a href="/ceia_swga/pages/planilla_salida_staff.php">
                        <span class="icono-reporte">📝</span> Gestión de Autorización de Salida de Personal
                    </a>
                    <p>Permite registrar y consultar las autorizaciones de salida para el personal.</p>
                </li>

                <li>
                    <a data-bs-toggle="collapse" href="#collapseMisc" role="button" aria-expanded="false" aria-controls="collapseMisc">
                        <span class="icono-reporte">🔗</span> Misceláneos
                    </a>
                    <p>Enlaces a plataformas académicas externas.</p>
                </li>
                <div class="collapse" id="collapseMisc">
                    <div class="card card-body" style="background-color: rgba(0,0,0,0.5); border: none;">
                        <a href="https://www.thinkwave.com/secure/login/" target="_blank">Thinkwave</a>
                        <a href="https://www.khanacademy.org/login" target="_blank">Khan Academy</a>
                        <a href="https://la.ixl.com/?partner=google&campaign=71585968&adGroup=127340411978&gad_source=1&gad_campaignid=71585968&gbraid=0AAAAADrr3Aoxof4aLGixlBgWG-gNefkcQ&gclid=CjwKCAjwi4PHBhA-EiwAnjTHuRzmWUrXTe4xr7ZS0h7VzeF3SMmBED1sbuGmAquyPDbRwoQ6wczduBoCOwMQAvD_BwE" target="_blank">IXL</a>
                    </div>
                </div>
            </ul>

            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/dashboard.php" class="btn">Volver</a> 
        </div>
    </div>

    <!-- Incluir Bootstrap JS para el colapso -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>