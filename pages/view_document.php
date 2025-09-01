<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/lib/Parsedown.php';
require_once __DIR__ . '/../src/config.php';

$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$content_html = '';
$page_title = 'Visor de Documentación';

if (isset($_GET['file'])) {
    $requested_file = $_GET['file'];

    // --- MEDIDA DE SEGURIDAD ---
    // Construir la ruta base y la ruta real del archivo solicitado
    $base_dir = realpath(__DIR__ . '/../funcionality');
    
    // Decodificar la URL para manejar caracteres especiales en el nombre del archivo/ruta
    $decoded_requested_file = urldecode($requested_file);

    $file_path = realpath($base_dir . '/' . $decoded_requested_file);

    // Validar que la ruta del archivo esté dentro del directorio Funcionality
    if ($file_path && strpos($file_path, $base_dir) === 0 && file_exists($file_path)) {
        $markdown_content = file_get_contents($file_path);
        if ($markdown_content !== false) {
            $Parsedown = new Parsedown();
            $content_html = $Parsedown->text($markdown_content);
            // Extraer un título del contenido del Markdown
            if (preg_match('/^# (.+)/m', $markdown_content, $matches)) {
                $page_title = htmlspecialchars($matches[1]);
            }
        } else {
            $content_html = '<p class="alerta">Error: No se pudo leer el archivo de documentación.</p>';
        }
    } else {
        $content_html = '<p class="alerta">Error: El archivo solicitado no es válido o no existe.</p>';
    }
} else {
    $content_html = '<p class="alerta">Error: No se ha especificado ningún archivo de documentación.</p>';
}
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
        <?= $content_html ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>