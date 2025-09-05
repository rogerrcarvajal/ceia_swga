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
$page_title = "Documentación del Módulo de Late-Pass";
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
        <h1>Documentación del Módulo de Late-Pass (Pase de Llegada Tarde)</h1>
<h2>1. Propósito del Módulo</h2>
<p>El Módulo de Late-Pass tiene como finalidad gestionar y registrar de manera formal las llegadas tardes de los estudiantes. Esta funcionalidad es crucial para mantener un registro disciplinario y de asistencia preciso, permitiendo a la administración del colegio llevar un control automatizado de los retardos y emitir un justificativo impreso para que el estudiante pueda ingresar a su aula de clases.</p>
<p>El módulo está centralizado en el <strong>Menú de Late-Pass</strong> (<code>pages/menu_latepass.php</code>).</p>
<h2>2. Flujo de Trabajo y Componentes</h2>
<p>La operativa del módulo es un proceso lineal y bien definido que involucra la identificación del estudiante, el registro del retardo y la generación de un comprobante en PDF.</p>
<h3>a. Gestión y Generación de Late-Pass</h3>
<ul>
  <li><strong>Página Principal</strong>: <code>pages/gestion_latepass.php</code></li>
  <li><strong>Archivos Involucrados</strong>:</li>
  <li><code>public/js/gestion_latepass.js</code> (Lógica del Frontend).</li>
  <li><code>api/consultar_latepass.php</code> (API para buscar al estudiante y registrar el pase).</li>
  <li><code>src/reports_generators/generar_latepass_pdf.php</code> (Script para crear el PDF).</li>
  <li><strong>Flujo de Operación</strong>:</li>
  <li>1.  <strong>Búsqueda del Estudiante</strong>: La página <code>gestion_latepass.php</code> presenta una interfaz donde el administrador puede buscar a un estudiante por su número de cédula.</li>
  <li>2.  <strong>Entrada de Cédula y Consulta (AJAX)</strong>: El administrador introduce la cédula del estudiante y presiona un botón de búsqueda. El script <code>gestion_latepass.js</code> captura este evento y realiza una llamada asíncrona (fetch) a la API <code>api/consultar_latepass.php</code>.</li>
  <li>3.  <strong>Validación en el Backend</strong>: La API <code>consultar_latepass.php</code> recibe la cédula y realiza una consulta a la base de datos (probablemente en la tabla <code>estudiantes</code>) para encontrar al estudiante y verificar que esté inscrito y activo en el período escolar actual.</li>
  <li>4.  <strong>Presentación de Datos</strong>: Si el estudiante es encontrado, la API devuelve sus datos (nombre, apellido, grado, etc.) en formato JSON. El script <code>gestion_latepass.js</code> recibe esta respuesta y la utiliza para rellenar los campos correspondientes en el formulario, mostrando la información del estudiante en pantalla.</li>
  <li>5.  <strong>Registro del Late-Pass</strong>: El administrador puede añadir una observación (opcional) y luego hace clic en el botón "Generar Pase". Esto no solo prepara la generación del PDF, sino que también realiza una inserción en la tabla <code>latepass</code> de la base de datos, registrando el ID del estudiante, la fecha y la hora del retardo.</li>
  <li>6.  <strong>Generación del PDF</strong>: Una vez registrado el retardo, el sistema invoca al script <code>src/reports_generators/generar_latepass_pdf.php</code>. Este script toma los datos del estudiante y del retardo recién creado.</li>
  <li>7.  <strong>Creación del Comprobante</strong>: Utilizando la librería FPDF, el script <code>generar_latepass_pdf.php</code> crea un documento PDF con un formato oficial que incluye el logo del colegio, los datos del estudiante, la fecha, la hora y un espacio para la firma o sello de la administración. Este PDF se muestra al administrador, listo para ser impreso.</li>
</ul>
<h2>3. Lógica de Negocio Clave</h2>
<ul>
  <li><strong>Registro Histórico</strong>: Cada vez que se genera un pase, queda un registro permanente en la tabla <code>latepass</code>. Esto es fundamental para el <strong>Módulo de Reportes</strong>, que puede explotar esta información para generar estadísticas de retardos por estudiante, por grado o por período.</li>
  <li><strong>Justificativo Físico</strong>: El PDF generado no es solo una notificación, sino un documento físico que formaliza el permiso de entrada al aula. Esto cierra el ciclo del proceso administrativo para una llegada tarde.</li>
  <li><strong>Integración con Estudiantes</strong>: El módulo está directamente ligado al Módulo de Estudiantes. No se puede generar un pase para alguien que no exista como estudiante activo en el sistema, asegurando la integridad de los datos.</li>
</ul>

HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>