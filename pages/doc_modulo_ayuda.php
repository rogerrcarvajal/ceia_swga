<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Access control
$rol = isset($_SESSION['usuario']['rol']) ? $_SESSION['usuario']['rol'] : '';
if ($rol !== 'master' && $rol !== 'admin') {
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$page_title = "Documentación del Módulo de Ayuda";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - <?= $page_title ?></title>
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
        <h1><?= $page_title ?></h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="document-container">
        <?php echo <<<HTML
        <h1>Documentación General del Módulo de Ayuda</h1>
<h2>1. Propósito del Módulo</h2>
<p>El <strong>Módulo de Ayuda</strong> es un componente central del sistema SWGA, diseñado para proporcionar soporte y documentación tanto a usuarios finales como a desarrolladores. Su objetivo es centralizar el acceso a guías de usuario, manuales técnicos y explicaciones detalladas sobre la funcionalidad y la lógica de negocio de cada módulo del sistema.</p>
<p>Este módulo actúa como un portal de conocimiento, mejorando la experiencia de usuario al ofrecer respuestas claras a posibles dudas y facilitando el mantenimiento y la futura expansión del sistema al tener la documentación técnica fácilmente accesible.</p>
<hr>
<h2>2. Componentes Principales</h2>
<p>El módulo se compone de varios archivos que trabajan en conjunto para ofrecer una experiencia fluida:</p>
<ol>
  <li><strong><code>pages/menu_ayuda.php</code></strong>: Es la página principal y el punto de entrada al módulo. Presenta al usuario un menú organizado desde donde puede navegar hacia el manual de usuario o la documentación técnica de los diferentes módulos del sistema.</li>
  <li><strong><code>pages/view_document.php</code></strong>: Un visor dinámico y seguro encargado de leer archivos de documentación en formato Markdown (<code>.md</code>), convertirlos a HTML y presentarlos en pantalla de una manera legible y estilizada. Es el motor que permite renderizar todo el contenido del módulo.</li>
  <li><strong><code>src/lib/Parsedown.php</code></strong>: Una librería de PHP externa y robusta que se especializa en la conversión de sintaxis Markdown a HTML. Es la dependencia clave que <code>view_document.php</code> utiliza para interpretar los archivos <code>.md</code>.</li>
  <li><strong>Directorio <code>Funcionality/</code></strong>: Este directorio es el repositorio central de toda la documentación en formato Markdown. Está estructurado en subcarpetas, una por cada módulo del sistema (<code>Módulo Estudiante</code>, <code>Módulo Staff</code>, etc.), conteniendo los archivos <code>.md</code> que explican en detalle cada proceso y archivo del sistema.</li>
</ol>
<hr>
<h2>3. Flujo de Operación</h2>
<p>El flujo de interacción del usuario con el Módulo de Ayuda es el siguiente:</p>
<ol>
  <li><strong>Acceso</strong>: El usuario accede al módulo a través del enlace en la barra de navegación principal, que lo dirige a <code>pages/menu_ayuda.php</code>.</li>
  <li><strong>Selección</strong>: En el menú de ayuda, el usuario elige qué documento desea consultar. Las opciones incluyen el "Manual de Usuario" o la documentación técnica de un módulo específico (ej. "Módulo Estudiante").</li>
  <li><strong>Visualización</strong>: Al hacer clic en un enlace, el navegador realiza una petición a <code>pages/view_document.php</code>, pasando la ruta del archivo <code>.md</code> a visualizar como un parámetro en la URL (ej. <code>?file=Módulo Estudiante/Funcionalidad_Modulo_Estudiantes.md</code>).</li>
  <li><strong>Procesamiento</strong>:
    <ul>
      <li><code>view_document.php</code> recibe la petición. Por seguridad, verifica que el archivo solicitado se encuentre dentro del directorio <code>Funcionality/</code>.</li>
      <li>Lee el contenido del archivo Markdown especificado.</li>
      <li>Utiliza la librería <code>Parsedown.php</code> para transformar el contenido Markdown en código HTML.</li>
      <li>Inserta el HTML resultante en una plantilla de página web, que incluye la barra de navegación, estilos CSS y un botón para volver al menú principal.</li>
    </ul>
  </li>
  <li><strong>Renderizado</strong>: El servidor devuelve la página HTML completa al navegador del usuario, que la muestra de forma estilizada y fácil de leer.</li>
</ol>

HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>