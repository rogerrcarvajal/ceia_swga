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
$page_title = "Documentación del Módulo de Estudiantes";
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
        <h1>Documentación del Módulo de Estudiantes</h1>
<h2>1. Propósito del Módulo</h2>
<p>El Módulo de Estudiantes es el núcleo del sistema de gestión académica. Permite administrar de forma integral toda la información relacionada con los estudiantes, desde su inscripción inicial hasta la gestión de sus datos personales, familiares, médicos y su vinculación con los períodos escolares.</p>
<h2>2. Flujo de Trabajo y Componentes</h2>
<p>El módulo se compone de varias páginas y APIs que trabajan en conjunto. El punto de entrada es el <strong>Menú de Gestión de Estudiantes</strong> (<code>pages/menu_estudiantes.php</code>).</p>
<h3>a. Inscripción de Nuevos Estudiantes</h3>
<ul>
  <li><strong>Página</strong>: <code>pages/planilla_inscripcion.php</code></li>
  <li><strong>Propósito</strong>: Ofrece un formulario completo para registrar a un nuevo estudiante en el sistema. Este formulario captura no solo los datos del estudiante, sino también la información de sus padres y su ficha médica básica.</li>
  <li><strong>Lógica</strong>: Al enviar el formulario, los datos son procesados por un script (no visible en el análisis actual, pero probablemente una API dedicada) que crea los registros correspondientes en las tablas <code>estudiantes</code>, <code>padres</code>, <code>madres</code> y <code>fichas_medicas</code> de la base de datos.</li>
</ul>
<h3>b. Gestión Integral de Expedientes</h3>
<p>Esta es la funcionalidad principal del módulo, donde los administradores pasan la mayor parte del tiempo.</p>
<ul>
  <li><strong>Página Principal</strong>: <code>pages/administrar_planilla_estudiantes.php</code></li>
  <li><strong>Archivos Involucrados</strong>:</li>
  <li><code>public/js/admin_estudiantes.js</code> (Lógica del Frontend)</li>
  <li><code>api/obtener_estudiante.php</code> (API para obtener datos del estudiante)</li>
  <li><code>api/obtener_padre.php</code> (API para obtener datos del padre)</li>
  <li><code>api/obtener_madre.php</code> (API para obtener datos de la madre)</li>
  <li><code>api/obtener_ficha_medica.php</code> (API para obtener datos de la ficha médica)</li>
  <li><code>api/actualizar_estudiante.php</code> y las APIs de actualización correspondientes para padre, madre y ficha.</li>
  <li><strong>Flujo de Operación</strong>:</li>
  <li>1.  <strong>Visualización</strong>: La página muestra una lista de todos los estudiantes registrados. Un campo de búsqueda permite filtrar la lista dinámicamente para encontrar a un estudiante específico rápidamente.</li>
  <li>2.  <strong>Selección</strong>: El administrador hace clic en el nombre de un estudiante de la lista.</li>
  <li>3.  <strong>Carga de Datos (AJAX)</strong>: El archivo <code>admin_estudiantes.js</code> intercepta el clic. De forma asíncrona (sin recargar la página), realiza múltiples llamadas <code>fetch</code> a las APIs <code>obtener_*.php</code> para traer toda la información del estudiante, su padre, su madre y su ficha médica.</li>
  <li>4.  <strong>Presentación de Datos</strong>: La información recuperada se utiliza para rellenar cuatro formularios distintos que se muestran en la parte derecha de la pantalla.</li>
  <li>5.  <strong>Edición y Guardado</strong>: El administrador puede modificar los datos en cualquiera de los cuatro formularios y presionar el botón "Guardar Cambios" correspondiente a ese formulario.</li>
  <li>6.  <strong>Actualización (AJAX)</strong>: <code>admin_estudiantes.js</code> de nuevo intercepta el envío del formulario. Envía los datos actualizados a la API <code>actualizar_*.php</code> correspondiente mediante una solicitud <code>POST</code>.</li>
  <li>7.  <strong>Confirmación</strong>: La API procesa la actualización en la base de datos y devuelve un mensaje de éxito o error, que se muestra al administrador en la pantalla.</li>
</ul>
<h3>c. Asignación a Períodos Escolares</h3>
<p>Esta funcionalidad permite vincular a un estudiante con un período escolar activo y definir qué grado cursará.</p>
<ul>
  <li><strong>Páginas</strong>: <code>pages/lista_gestion_estudiantes.php</code> y <code>pages/gestionar_estudiantes.php</code></li>
  <li><strong>Flujo de Operación</strong>:</li>
  <li>1.  El administrador accede a <code>lista_gestion_estudiantes.php</code>, que muestra una lista de todos los estudiantes.</li>
  <li>2.  Al seleccionar un estudiante, es redirigido a <code>gestionar_estudiantes.php</code> con el ID del estudiante en la URL.</li>
  <li>3.  Esta página muestra los datos del estudiante y le permite, a través de un checkbox y un menú desplegable, asignarlo al período escolar que esté marcado como "activo" en el sistema y seleccionar el grado a cursar.</li>
  <li>4.  Al guardar, el sistema crea o actualiza un registro en la tabla <code>estudiante_periodo</code>, que es la que vincula a los estudiantes con los períodos.</li>
</ul>
<h3>d. Gestión de Vehículos Autorizados</h3>
<ul>
  <li><strong>Página</strong>: <code>pages/registro_vehiculos.php</code></li>
  <li><strong>Propósito</strong>: Aunque es una entidad separada, se gestiona desde el menú de estudiantes, ya que los vehículos están directamente relacionados con ellos. Esta sección permite registrar los vehículos autorizados para recoger a un estudiante.</li>
</ul>

HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>