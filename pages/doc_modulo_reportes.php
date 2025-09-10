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
<h3>1. Generar Planilla de Inscripción (<code>seleccionar_planilla.php</code>)</h3>
<ul>
<li><strong>Funcionalidad:</strong> Permite al administrador generar un PDF con la planilla de inscripción completa de un estudiante específico.</li>
<li><strong>Flujo de Trabajo:</strong>
<ol>
<li>La página carga una lista desplegable con todos los estudiantes inscritos en el período escolar activo.</li>
<li>El administrador selecciona un estudiante.</li>
<li>Al hacer clic en "Generar PDF", el ID del estudiante seleccionado se envía mediante un formulario GET al script <code>src/reports_generators/generar_planilla_pdf.php</code>.</li>
</ol>
</li>
<li><strong>Lógica Técnica:</strong> El script generador de PDF (inferido) recibe el ID, consulta toda la información asociada al estudiante (datos personales, padres, ficha médica) y la maqueta en un documento PDF detallado.</li>
</ul>
<hr>
<h3>2. Roster Actualizado (<code>roster_actual.php</code>)</h3>
<ul>
<li><strong>Funcionalidad:</strong> Ofrece una vista consolidada de todo el personal y los estudiantes activos en el período actual, con la opción de exportar esta vista a PDF.</li>
<li><strong>Flujo de Trabajo:</strong>
<ol>
<li>Al cargar la página, el backend consulta y muestra en dos tablas separadas las listas de "Staff" y "Estudiantes" activos.</li>
<li>Un botón "Generar PDF del Roster" envía un formulario (sin necesidad de parámetros adicionales) al script <code>src/reports_generators/generar_roster_pdf.php</code>.</li>
</ol>
</li>
<li><strong>Lógica Técnica:</strong> El script generador de PDF replica la misma consulta de la página principal para obtener las listas de personal y estudiantes activos y las formatea en un documento PDF.</li>
</ul>
<hr>
<h3>3. Gestionar Reportes (<code>gestionar_reportes.php</code>)</h3>
<ul>
<li><strong>Funcionalidad:</strong> Serve como un panel centralizado para previsualizar y generar múltiples reportes de listas categorizadas.</li>
<li><strong>Flujo de Trabajo:</strong>
<ol>
<li><strong>Carga Inicial:</strong> Al cargar la página, el backend ejecuta todas las consultas necesarias para cada categoría de reporte (Estudiantes, Staff Administrativo, Staff Docente, etc.) y las renderiza en tablas HTML que permanecen ocultas inicialmente.</li>
<li><strong>Interacción del Usuario:</strong> El usuario hace clic en una categoría en el menú lateral (ej. "Vehículos Autorizados").</li>
<li><strong>Visualización:</strong> Un script de JavaScript simple se activa para mostrar la sección de vista previa correspondiente a la categoría seleccionada.</li>
<li><strong>Generación de PDF:</strong> Cada sección de vista previa tiene su propio botón "Generar PDF", que apunta a un script generador de PDF específico para esa categoría (ej. <code>generar_lista_vehiculos_autorizados_PDF.php</code>).</li>
</ol>
</li>
<li><strong>Lógica Técnica:</strong> Este enfoque precarga todos los datos, haciendo que la experiencia de usuario para cambiar entre vistas previas sea instantánea. La modularidad es alta, ya que cada reporte tiene su propio script generador de PDF, facilitando el mantenimiento.</li>
</ul>
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
<hr>
<h3>3. Reportes Asociados al Módulo</h3>
<p>El módulo de Staff está vinculado a varios reportes importantes que se generan desde el Módulo de Reportes.</p>
<ul>
<li><strong><code>generar_lista_staff_admin_PDF.php</code></strong>: Genera un listado en PDF del personal administrativo.</li>
<li><strong><code>generar_lista_staff_docente_PDF.php</code></strong>: Genera un listado en PDF del personal docente.</li>
<li><strong><code>generar_lista_staff_mantenimiento_PDF.php</code></strong>: Genera un listado en PDF del personal de mantenimiento.</li>
<li><strong><code>pdf_movimiento_staff.php</code></strong>: Genera un reporte detallado de los movimientos (entradas y salidas) del personal en un rango de fechas específico.</li>
</ul>
<p>Estos scripts consultan las tablas <code>staff</code> y <code>movimientos_staff</code> y utilizan la librería <code>FPDF</code> para formatear y presentar los datos en documentos PDF.</p>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>