<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
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
<h1>Análisis de Funcionalidad: Módulo Staff</h1>
<p>Este documento detalla el flujo de trabajo y la lógica de negocio exclusivos del Módulo de Staff, cuya responsabilidad es el registro y la gestión de la información del personal de la institución.</p>
<hr>
<h3>Arquitectura y Propósito</h3>
<p>El propósito de este módulo es manejar el ciclo de vida de los datos del personal (docentes, administrativos, etc.). Su arquitectura se basa en un modelo clásico de PHP, donde las acciones se procesan en el backend mediante la recarga de páginas.</p>
<p><strong>Nota Importante:</strong> Funcionalidades como la generación de códigos QR, el registro de entradas/salidas y la creación de reportes de asistencia para el personal <strong>no residen en este módulo</strong>, sino en los módulos de <strong>Late-Pass</strong> y <strong>Reportes</strong>, respectivamente.</p>
<hr>
<h3>1. <code>profesores_registro.php</code> (Registro y Listado General)</h3>
<p>Esta página funciona como el panel de control principal para la administración del personal.</p>
<h4>Doble Funcionalidad</h4>
<ol>
<li><strong>Formulario de Registro:</strong> Proporciona una interfaz para crear un nuevo registro de personal, capturando sus datos básicos como nombre, cédula, teléfono, email y categoría (ej. "Staff Docente"). En este paso, el personal solo se crea en el sistema, pero aún no está vinculado a un período escolar.</li>
<li><strong>Lista Maestra:</strong> Muestra una lista de <strong>todo</strong> el personal registrado en la base de datos. Gracias a una consulta <code>LEFT JOIN</code>, la lista indica de forma clara si cada persona ya ha sido asignada al período escolar activo, proporcionando un resumen visual del estado de la plantilla.</li>
</ol>
<h4>Flujo de Trabajo</h4>
<p>El flujo es directo: un administrador crea un nuevo registro de personal. Una vez creado, el miembro del personal aparece en la "Lista Maestra". Junto a su nombre, un enlace de <strong>"Gestionar"</strong> permite pasar a la siguiente etapa del ciclo de vida.</p>
<hr>
<h3>2. <code>gestionar_profesor.php</code> (Edición y Asignación a Período)</h3>
<p>Esta página se dedica a la gestión detallada de un único miembro del personal, seleccionado desde la lista anterior.</p>
<h4>Funcionalidad</h4>
<ol>
<li><strong>Edición de Datos:</strong> Permite modificar la información básica del individuo (nombre, cédula, etc.).</li>
<li><strong>Asignación al Período Activo:</strong> Esta es la funcionalidad clave del módulo. Un formulario permite vincular al miembro del personal con el período escolar activo, especificando su <code>posición</code> (ej. "Grade 5 Teacher", "Director") y su rol de <code>homeroom_teacher</code>, si aplica. También permite desvincularlo.</li>
</ol>
<h4>Lógica de Negocio</h4>
<p>Al guardar los cambios, el script PHP actualiza los datos del profesor y gestiona su vínculo con el período escolar en la tabla <code>profesor_periodo</code>. Si se desmarca la casilla de asignación, el vínculo se elimina, pero el registro del profesor permanece en el sistema para futuras asignaciones.</p>
<hr>
<h3>Conclusión General del Módulo</h3>
<p>El Módulo Staff cumple de manera efectiva y robusta con sus dos responsabilidades principales: <strong>registrar al personal y asignarlo a un período escolar</strong>. Su lógica es clara y se centra exclusivamente en la gestión de los datos maestros del personal, dejando que otros módulos consuman esta información para sus propios fines.</p>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>