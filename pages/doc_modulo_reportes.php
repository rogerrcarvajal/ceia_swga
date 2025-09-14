<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$page_title = "Módulo de Reportes - Documentación Técnica del Sistema";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - <?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .content { color: white; text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black; }
        .content img { width: 180px; }
        .document-container { background-color: rgba(0, 0, 0, 0.3); color: white; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); margin: 20px auto; padding: 20px 40px; border-radius: 10px; max-width: 80%; text-align: left; }
        .document-container h1, .document-container h2, .document-container h3 { color: #a2ff96; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 5px; }
        .document-container h1 { font-size: 2em; }
        .document-container h2 { font-size: 1.75em; }
        .document-container h3 { font-size: 1.5em; }
        .document-container p { line-height: 1.6; }
        .document-container a { color: #87cefa; }
        .document-container code { background-color: rgba(255,255,255,0.1); padding: 2px 4px; border-radius: 3px; font-family: monospace; color: #87cefa; }
        .document-container pre { background-color: rgba(0,0,0,0.5); padding: 10px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; color: white; }
        .document-container ul, .document-container ol { padding-left: 20px; }
        .document-container blockquote { border-left: 5px solid rgba(255,255,255,0.5); padding-left: 15px; margin-left: 0; color: #ccc; }
        .alerta { color: #ffcccc; background-color: rgba(255,0,0,0.2); border-color: rgba(255,0,0,0.5); padding: 15px; border-radius: 5px; text-align: center; }
        .btn-back { display: inline-block; background-color: rgb(48, 48, 48); color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; text-align: center; margin-top: 20px;}
        .btn-back:hover { background-color: rgb(60, 60, 60); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="document-container">
        <?php echo <<<HTML
<h1>Análisis de Funcionalidad: Módulo de Reportes</h1>
<p>Este documento describe el flujo de trabajo y los componentes técnicos del "Módulo de Reportes", cuya función principal es la extracción de datos y su presentación en formato PDF.</p>
<hr>
<h3>Arquitectura General</h3>
<p>A diferencia de otros módulos interactivos del sistema, el Módulo de Reportes sigue una arquitectura clásica basada en PHP. La lógica principal reside en el backend, que se encarga de consultar la base de datos y renderizar la información. El uso de JavaScript es mínimo y se limita a mejorar la usabilidad de la interfaz, sin realizar llamadas a APIs para la obtención de datos.</p>
<hr>
<h3>Componentes Principales</h3>
<ul>
<li><strong><code>pages/menu_reportes.php</code></strong>: Menú principal para la selección de los diferentes tipos de reportes.</li>
<li><strong><code>pages/seleccionar_planilla.php</code></strong>: Interfaz para seleccionar un estudiante y generar su planilla de inscripción en PDF.</li>
<li><strong><code>src/reports_generators/roster_actual.php</code></strong>: Página que muestra el roster actual de estudiantes y staff, con opción a generar PDF.</li>
<li><strong><code>pages/gestionar_reportes.php</code></strong>: Panel para previsualizar y generar diversos reportes dinámicos en PDF.</li>
</ul>
<hr>
<h3>Flujo de Trabajo General</h3>
<ol>
<li>El administrador accede a <code>pages/menu_reportes.php</code>.</li>
<li>Desde el menú, selecciona el tipo de reporte deseado:
    <ul>
        <li>Para la planilla de inscripción de un estudiante, se dirige a <code>pages/seleccionar_planilla.php</code>, elige al estudiante y genera el PDF.</li>
        <li>Para el roster actual de estudiantes y staff, accede a <code>src/reports_generators/roster_actual.php</code> y desde allí puede generar el PDF.</li>
        <li>Para otros reportes dinámicos, navega a <code>pages/gestionar_reportes.php</code>, donde puede previsualizar y aplicar filtros antes de generar el PDF correspondiente.</li>
    </ul>
</li>
<li>Cada opción de reporte conduce a un script generador de PDF específico (ubicados en <code>src/reports_generators/</code>) que procesa los datos y envía el documento al navegador.</li>
</ol>
<hr>
<h3>Conclusión General del Módulo</h3>
<p>El "Módulo de Reportes" es robusto y cumple su propósito de manera efectiva y directa. Su arquitectura basada en PHP es adecuada para la tarea de generar vistas de datos estáticas.</p>
<ul>
<li><strong>Fortalezas:</strong>
<ul>
<li><strong>Claridad y Sencillez:</strong> La lógica es directa y fácil de mantener.</li>
<li><strong>Modularidad:</strong> El uso de un script generador de PDF dedicado para cada reporte es una excelente práctica de diseño que aísla la lógica de cada uno.</li>
<li><strong>Eficiencia:</strong> En <code>gestionar_reportes.php</code>, la precarga de datos permite una navegación fluida entre las diferentes vistas previas sin esperas adicionales.</li>
</ul>
</li>
</ul>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>