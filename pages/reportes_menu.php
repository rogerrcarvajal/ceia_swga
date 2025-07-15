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
    <title>Reportes del Sistema - CEIA</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 20px; border-radius: 10px; max-width: 50%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .content { color: white; text-align: center; margin-top: 30px; text-shadow: 1px 1px 2px black;}
        .content img { width: 180px;}
        .content h2 { margin-bottom: 25px;}
        /* Estilos para la lista de reportes */
        .lista-reportes { backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); list-style: none; padding: 0; text-align: left;}
        .lista-reportes li { background-color: rgba(255, 255, 255, 0.1); margin-bottom: 15px; border-radius: 5px; transition: background-color 0.3s ease;}
        .lista-reportes li:hover { background-color: rgba(255, 255, 255, 0.25);}
        .lista-reportes a { display: block; padding: 15px; color: white; text-decoration: none; font-size: 1.1em;}
        .lista-reportes p { margin: 0; padding: 0 15px 15px 15px; font-size: 0.9em; color: #ccc;}
        .lista-reportes .icono-reporte { margin-right: 12px; font-size: 1.2em;}
    </style>    
</head>

<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Reportes del Sistema</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="formulario-contenedor">
        <div class="content">       
            <ul class="lista-reportes">
                <li>
                    <a href="/pages/seleccionar_planilla.php" target="_blank">
                        <span class="icono-reporte">📋</span> Planilla de Inscripción
                    </a>
                    <p>Permite la seleccion de un estudiante para obtener la planilla de inscripcion.</p>
                </li>

                <li>
                    <a href= "/src/reports_generators/roster_actual.php" target="_blank">
                        <span class="icono-reporte">📄</span> Roster Actualizado
                    </a>
                    <p>Vista previa del personal admininstrativo y docente, además un listado de estudiantes por grado, con opciones para exportar a PDF.</p>
                </li>

                <li>
                    <a href="/../src/reports_generators/admin_late_pass.php" target="_blank">
                        <span class="icono-reporte">📷</span> Control de acceso (Late-Pass)
                    </a>
                    <p>Formulario para generar y exportar códigos QR para el control de acceso de estudiantes, profesores y vehículos.</p>
                </li>
                
            </ul>

            <br>
            <!-- Botón para volver al Home -->
            <a href="/pages/dashboard.php" class="btn">Volver</a> 
        </div>
    </div>
</body>
</html>