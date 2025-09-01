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
$page_title = "Documentación del Módulo de Reportes";
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
        <h1>Documentación del Módulo de Reportes</h1>
<h2>1. Propósito del Módulo</h2>
<p>El Módulo de Reportes es una herramienta de inteligencia de negocio que centraliza la generación de documentos y listados en formato PDF. Su función es tomar los datos almacenados en el sistema y presentarlos de una manera organizada, legible y lista para ser impresa, archivada o distribuida.</p>
<p>Este módulo es esencialmente una interfaz que invoca a diferentes scripts especializados en la creación de reportes específicos, utilizando las librerías FPDF y phpqrcode como base tecnológica. El punto de entrada principal es el <strong>Menú de Reportes</strong> (<code>pages/menu_reportes.php</code>) que dirige a la página de gestión.</p>
<h2>2. Flujo de Trabajo y Componentes</h2>
<ul>
  <li><strong>Página Principal</strong>: <code>pages/gestionar_reportes.php</code></li>
  <li><strong>Directorio de Generadores</strong>: <code>src/reports_generators/</code></li>
</ul>
<p>El flujo general es el siguiente:</p>
<ol>
  <li>El administrador navega a la página <code>gestionar_reportes.php</code>.</li>
  <li>La página presenta un formulario o una serie de botones, cada uno correspondiente a un reporte específico.</li>
  <li>Para reportes que requieren parámetros (como un rango de fechas), el administrador los introduce.</li>
  <li>Al hacer clic en un botón de "Generar", el formulario se envía o se redirige al script PHP correspondiente dentro de la carpeta <code>src/reports_generators/</code>.</li>
  <li>El script PHP ejecuta las consultas necesarias a la base de datos, procesa los datos y utiliza la librería FPDF para construir el documento PDF.</li>
  <li>El script finaliza enviando el PDF generado directamente al navegador del usuario para su visualización o descarga.</li>
</ol>
<h2>3. Listado de Reportes y su Funcionalidad</h2>
<p>A continuación se detalla cada uno de los reportes disponibles en el sistema:</p>
<h3>Reportes de Estudiantes</h3>
<ul>
  <li><strong><code>generar_lista_estudiantes_PDF.php</code></strong>: Genera un listado completo de todos los estudiantes inscritos en el período escolar activo, mostrando detalles clave como cédula, nombres, apellidos y grado.</li>
  <li><strong><code>generar_planilla_pdf.php</code></strong>: Genera la planilla de inscripción completa de un estudiante específico, incluyendo sus datos, los de sus padres y su ficha médica. Es una réplica digital del formulario de inscripción.</li>
  <li><strong><code>generar_qr_pdf.php</code></strong>: Crea un documento PDF con el código QR de un estudiante. Este QR usualmente contiene la cédula del estudiante y se usa para el sistema de control de acceso.</li>
  <li><strong><code>generar_roster_pdf.php</code></strong>: Genera el "roster" o listado de un grado y sección específicos, mostrando los estudiantes que pertenecen a esa clase. Es útil para los docentes.</li>
</ul>
<h3>Reportes de Staff (Personal)</h3>
<ul>
  <li><strong><code>generar_lista_staff_admin_PDF.php</code></strong>: Genera un listado del personal con el cargo "Administrativo".</li>
  <li><strong><code>generar_lista_staff_docente_PDF.php</code></strong>: Genera un listado del personal con el cargo "Docente".</li>
  <li><strong><code>generar_lista_staff_mantenimiento_PDF.php</code></strong>: Genera un listado del personal con el cargo "Mantenimiento".</li>
  <li><strong><code>generar_qr_staff_pdf.php</code></strong>: Crea el carnet o ficha con el código QR de un miembro del personal para el control de acceso.</li>
  <li><strong><code>pdf_movimiento_staff.php</code></strong>: Reporte de auditoría que muestra el historial de entradas y salidas de los miembros del personal, usualmente filtrado por un rango de fechas.</li>
</ul>
<h3>Reportes de Vehículos</h3>
<ul>
  <li><strong><code>generar_lista_vehiculos_autorizados_pdf.php</code></strong>: Genera un listado de todos los vehículos registrados en el sistema, indicando a qué estudiantes están autorizados a transportar.</li>
  <li><strong><code>generar_qr_vehiculo_pdf.php</code></strong>: Genera la identificación con el código QR para un vehículo, que puede ser pegada en el parabrisas para un rápido escaneo en la garita de seguridad.</li>
  <li><strong><code>pdf_movimientos_vehiculos.php</code></strong>: Reporte de auditoría que muestra el historial de entradas y salidas de los vehículos autorizados.</li>
</ul>
<h3>Reportes de Gestión y Disciplina</h3>
<ul>
  <li><strong><code>generar_latepass_pdf.php</code></strong>: Como se describió en su propio módulo, genera el comprobante impreso para una llegada tarde específica.</li>
</ul>
<h2>4. Lógica de Negocio Clave</h2>
<ul>
  <li><strong>Centralización</strong>: El módulo agrupa la lógica de presentación de datos en un solo lugar, facilitando el mantenimiento y la creación de nuevos reportes.</li>
  <li><strong>Consistencia Visual</strong>: Al usar FPDF y plantillas comunes, todos los reportes mantienen una apariencia profesional y consistente con la imagen de la institución (logos, colores, etc.).</li>
  <li><strong>Seguridad</strong>: Aunque no es visible directamente, es probable que cada script de generación de reportes verifique la sesión del usuario para asegurar que solo personal autorizado pueda generar y acceder a la información sensible.</li>
</ul>

HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>