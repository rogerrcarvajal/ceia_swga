<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

// Declaración de variables
$mensaje = "";

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== 'master') {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo el Usuario Master puede gestionar el módulo de mantenimiento.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo el Usuario Master puede gestionar el módulo de mantenimiento."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
}

// Obtener lista de estudiantes
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Administración de Late-Pass</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 20px; border-radius: 10px; max-width: 50%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .content { color: white; text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black;}
        .content img { width: 180px;}
        .content h2 { margin-bottom: 25px;}
        /* Estilos para la lista de menu */
        .lista-menu { backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); list-style: none; padding: 0; text-align: left;}
        .lista-menu li { background-color: rgba(255, 255, 255, 0.1); margin-bottom: 15px; border-radius: 5px; transition: background-color 0.3s ease;}
        .lista-menu li:hover { background-color: rgba(255, 255, 255, 0.25);}
        .lista-menu a { display: block; padding: 15px; color: white; text-decoration: none; font-size: 1.1em;}
        .lista-menu p { margin: 0; padding: 0 15px 15px 15px; font-size: 0.9em; color: #ccc;}
        .lista-menu .icono-reporte { margin-right: 12px; font-size: 1.2em;}
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Administración de Late-Pass</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="formulario-contenedor">
        <div class="content">       
            <ul class="lista-menu">
                <li>
                    <a href="/ceia_swga/pages/periodos_escolares.php">
                        <span class="icono-reporte">📋</span> Establecer Períodos Escolares
                    </a>
                    <p>Permite crear y activar un nuevo Periodo escolar, el cual vinculara información de Estudiantes, Madre, Madre y Staff con el período escolar activo.</p>
                </li>

                <li>
                    <a href= "/ceia_swga/pages/configurar_usuarios.php">
                        <span class="icono-reporte">📄</span> Gestión de Usuarios del Sistema
                    </a>
                    <p>Permite laa creación y gestión de usuarios del sistema, vinculando al staff registrado como un usuario en el periodo escolar activo.</p>
                </li>

                <li>
                    <a href="/ceia_swga/pages/backup_db.php">
                        <span class="icono-reporte">🗃️</span> Gestion de Backup
                    </a>
                    <p>Permite la creación y restauración de respaldos de la base de datos.</p>
                </li>

            </ul>

            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/dashboard.php" class="btn">Volver</a> 
        </div>
    </div>
</body>
</html>