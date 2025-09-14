<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$page_title = "Módulo de Ayuda - Documentación Técnica del Sistema";
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
<h1>Análisis de Funcionalidad: Módulo de Ayuda</h1>
<p>Este documento describe la estructura y el propósito del Módulo de Ayuda.</p>
<hr>
<h3>Arquitectura y Propósito</h3>
<p>El Módulo de Ayuda (<code>menu_ayuda.php</code>) funciona como un portal de documentación centralizado para todo el sistema SWGA. Su objetivo es proporcionar dos niveles de soporte:</p>
<ol>
<li><strong>Soporte al Usuario Final:</strong> A través del <strong>Manual de Usuario</strong> (<code>doc_manual_usuario.php</code>), se ofrece una guía de alto nivel sobre la navegabilidad y el flujo de trabajo general del sistema, permitiendo a los administradores y operadores comprender cómo utilizar cada módulo de manera efectiva.</li>
<li><strong>Soporte Técnico:</strong> A través de una serie de documentos técnicos (<code>doc_modulo_....php</code>), se proporciona un análisis detallado de la arquitectura, los componentes y la lógica de negocio de cada módulo individual. Esta documentación es invaluable para los desarrolladores, el personal de TI o cualquier persona que necesite comprender el funcionamiento interno del sistema para realizar mantenimiento, solucionar problemas o desarrollar nuevas funcionalidades.</li>
</ol>
<h3>Componentes Clave</h3>
<ul>
<li><strong><code>menu_ayuda.php</code></strong>: La página principal del módulo, que contiene los enlaces a toda la documentación.</li>
<li><strong><code>doc_manual_usuario.php</code></strong>: El manual de usuario general.</li>
<li><strong><code>doc_modulo_....php</code></strong>: Una página dedicada para la documentación técnica de cada módulo principal del sistema (Estudiantes, Staff, Late-Pass, Reportes, Mantenimiento y este mismo módulo de Ayuda).</li>


</ul>
<h3>Conclusión</h3>
<p>El Módulo de Ayuda es un componente fundamental para la sostenibilidad y mantenibilidad a largo plazo del sistema. Al centralizar tanto la documentación de usuario como la técnica, se asegura que todo el conocimiento sobre el sistema esté accesible y organizado, reduciendo la dependencia de desarrolladores individuales y facilitando la capacitación de nuevo personal.</p>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>