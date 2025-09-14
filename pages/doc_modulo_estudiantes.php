<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$page_title = "Módulo de Estudiantes - Documentación Técnica del Sistema";
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
<h1>Análisis de Funcionalidad y Lógica: Módulo Estudiante</h1>
<p>Este documento detalla el análisis del flujo de trabajo, las interacciones de frontend/backend y la lógica de negocio para el Módulo de Estudiantes.</p>
<h2>Componentes Principales</h2>
<ul>
<li><strong><code>pages/planilla_inscripcion.php</code></strong>: Formulario para el ingreso de nuevos estudiantes.</li>
<li><strong><code>pages/administrar_planilla_estudiantes.php</code></strong>: Panel para la consulta y modificación de expedientes de estudiantes existentes.</li>
<li><strong><code>pages/asignar_estudiante_periodo.php</code></strong>: Panel para vincular estudiantes a un período escolar y asignarles un grado.</li>
</ul>
<hr>
<h3>1. <code>planilla_inscripcion.php</code> (Creación de Expedientes)</h3>
<p>Este componente gestiona el registro inicial de un estudiante, incluyendo sus datos personales, la información de sus padres y la ficha médica.</p>
<h4>Flujo de Usuario</h4>
<ol>
<li>El administrador completa los datos del estudiante.</li>
<li>Al llegar a la sección "Datos del Padre/Madre", introduce el número de cédula o pasaporte.</li>
<li>El sistema, de forma automática, busca si el representante ya existe en la base de datos.</li>
<li><strong>Si el representante existe:</strong> La interfaz muestra la opción <strong>"Vincular"</strong>. Al seleccionarla, los campos de datos de esa persona se bloquean y el sistema se prepara para usar el ID del registro existente.</li>
<li><strong>Si el representante no existe:</strong> El administrador procede a llenar todos los campos para crear un nuevo registro para el padre/madre.</li>
<li>Al hacer clic en "Guardar Planilla", el backend procesa la solicitud en una transacción segura.</li>
</ol>
<h4>Componentes Técnicos</h4>
<ul>
<li><strong>Frontend:</strong> JavaScript integrado (<code>inline</code>) en el propio archivo <code>.php</code>.</li>
<li><strong>API Involucrada:</strong> <code>GET /api/buscar_representante.php</code> para verificar la existencia de los padres/madres.</li>
<li><strong>Lógica de Backend:</strong> El script PHP gestiona todo el proceso como una <strong>transacción de base de datos</strong>, garantizando la integridad de los datos.</li>
</ul>
<hr>
<h3>2. <code>administrar_planilla_estudiantes.php</code> (Gestión de Expedientes)</h3>
<p>Este panel permite la visualización y actualización de la información de cualquier estudiante registrado en el sistema.</p>
<h4>Flujo de Usuario</h4>
<ol>
<li>El administrador visualiza una lista completa de estudiantes y selecciona uno.</li>
<li>El sistema carga dinámicamente su expediente completo en cuatro formularios independientes (Estudiante, Padre, Madre, Ficha Médica).</li>
<li>El administrador puede modificar y guardar los cambios de forma individual para cada sección.</li>
</ol>
<h4>Componentes Técnicos</h4>
<ul>
<li><strong>Frontend:</strong> La lógica reside en el archivo externo <code>/public/js/admin_estudiantes.js</code>.</li>
<li><strong>APIs de Lectura (GET):</strong> Se usan múltiples APIs para obtener los datos por separado: <code>obtener_estudiante.php</code>, <code>obtener_padre.php</code>, <code>obtener_madre.php</code>, <code>obtener_ficha_medica.php</code>.</li>
<li><strong>APIs de Escritura (POST):</strong> Cada formulario tiene su propio endpoint para las actualizaciones: <code>actualizar_estudiante.php</code>, <code>actualizar_padre.php</code>, etc.</li>
</ul>
<hr>
<h3>3. <code>asignar_estudiante_periodo.php</code> (Asignación a Período)</h3>
<p>Esta funcionalidad es el puente entre el registro de un estudiante y su participación activa en la vida académica.</p>
<h4>Flujo de Usuario</h4>
<ol>
<li>El administrador selecciona un período escolar de una lista.</li>
<li>La interfaz se actualiza dinámicamente, mostrando dos listas:
<ul>
<li>A la izquierda, los estudiantes <strong>ya asignados</strong> a ese período.</li>
<li>A la derecha, en un formulario, los estudiantes <strong>disponibles para asignar</strong> (aquellos que no están en ningún período).</li>
</ul>
</li>
<li>El administrador elige un estudiante disponible, le asigna un grado y hace clic en "Asignar".</li>
<li>La asignación se procesa en segundo plano, y las listas se actualizan automáticamente sin recargar la página.</li>
</ol>
<h4>Componentes Técnicos</h4>
<ul>
<li><strong>Frontend:</strong> La lógica reside en <code>/public/js/admin_asignar_estudiante.js</code> (inferido), que orquesta las llamadas a las APIs.</li>
<li><strong>APIs Involucradas:</strong>
<ul>
<li><code>GET /api/obtener_estudiantes_por_periodo.php</code>: Para poblar la lista del panel izquierdo.</li>
<li><code>GET /api/obtener_estudiantes_no_asignados.php</code>: Para poblar el menú de estudiantes disponibles.</li>
<li><code>POST /api/asignar_estudiante.php</code>: Para crear el vínculo en la tabla <code>estudiante_periodo</code>, registrando la asignación.</li>
</ul>
</li>
</ul>
<hr>
<h3>Conclusión sobre la Lógica de Negocio</h3>
<ul>
<li><strong>Relación 1-a-Muchos (Representantes):</strong> La implementación de la relación "un representante a muchos estudiantes" es un punto fuerte del sistema. Se maneja de forma robusta tanto en la creación (evitando duplicados) como en la gestión (los cambios en un padre se reflejan en todos sus representados).</li>
<li><strong>Ciclo de Vida del Estudiante:</strong> El módulo gestiona el ciclo de vida completo:
<ol>
<li><strong>Creación</strong> (<code>planilla_inscripcion.php</code>).</li>
<li><strong>Asignación</strong> a un período y grado (<code>asignar_estudiante_periodo.php</code>).</li>
<li><strong>Gestión y Actualización</strong> continua (<code>administrar_planilla_estudiantes.php</code>).</li>
</ol>
</li>
<li><strong>Arquitectura Moderna:</strong> El módulo combina de forma efectiva páginas clásicas renderizadas por el servidor con paneles dinámicos que consumen APIs y se actualizan en tiempo real, ofreciendo una experiencia de usuario fluida y eficiente.</li>
</ul>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>