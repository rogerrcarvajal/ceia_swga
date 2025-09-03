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
$page_title = "Documentación del Módulo de Staff";
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
        <h1>Documentación del Módulo de Staff</h1>
<h2>1. Propósito del Módulo</h2>
<p>El Módulo de Staff está diseñado para la gestión completa del personal de la institución, que incluye docentes, personal administrativo y de mantenimiento. El término "Profesor" se usa a menudo en el código como sinónimo de "miembro del Staff".</p>
<p>Este módulo permite registrar nuevos miembros del personal, gestionar su información, asignarlos a períodos escolares y controlar sus movimientos de entrada y salida del plantel, generando también los códigos QR para su identificación.</p>
<h2>2. Flujo de Trabajo y Componentes</h2>
<p>El módulo se articula a través de varias páginas, APIs y scripts que cubren todo el ciclo de vida de un miembro del personal en el sistema.</p>
<h3>a. Registro de Nuevo Personal</h3>
<ul>
  <li><strong>Página</strong>: <code>pages/profesores_registro.php</code></li>
  <li><strong>Propósito</strong>: Proporciona un formulario para dar de alta a un nuevo miembro del personal en el sistema.</li>
  <li><strong>Lógica de Negocio</strong>:</li>
  <li>1.  El administrador completa los datos personales del miembro del staff (cédula, nombres, apellidos, teléfono, cargo, etc.).</li>
  <li>2.  Al enviar el formulario, los datos son validados y enviados a una API (probablemente <code>api/asignar_profesores.php</code> o similar) que inserta un nuevo registro en la tabla <code>staff</code> de la base de datos.</li>
  <li>3.  El sistema genera automáticamente un código QR único asociado a la cédula del miembro del personal.</li>
</ul>
<h3>b. Gestión de Personal Existente</h3>
<ul>
  <li><strong>Página Principal</strong>: <code>pages/gestionar_profesor.php</code></li>
  <li><strong>Archivos Involucrados</strong>:</li>
  <li><code>public/js/admin_profesores.js</code> (Lógica del Frontend para la gestión).</li>
  <li><code>api/obtener_profesores.php</code> (API para listar todos los profesores).</li>
  <li><code>api/actualizar_profesores.php</code> (API para guardar cambios).</li>
  <li><code>pages/eliminar_profesor.php</code> (Script para eliminar un registro).</li>
  <li><strong>Flujo de Operación</strong>:</li>
  <li>1.  <strong>Visualización y Búsqueda</strong>: La página muestra una lista de todo el personal registrado. Un campo de búsqueda permite filtrar los resultados para una localización rápida.</li>
  <li>2.  <strong>Selección y Edición</strong>: El administrador puede seleccionar un miembro del personal para ver sus detalles y activar el modo de edición.</li>
  <li>3.  <strong>Actualización (AJAX)</strong>: El script <code>admin_profesores.js</code> maneja la lógica de la interfaz. Al guardar los cambios, se realiza una llamada asíncrona a <code>api/actualizar_profesores.php</code> para actualizar la información en la base de datos sin necesidad de recargar la página.</li>
  <li>4.  <strong>Eliminación</strong>: Se proporciona una opción para eliminar a un miembro del personal, que probablemente redirige al script <code>eliminar_profesor.php</code> para procesar la baja.</li>
</ul>
<h3>c. Control de Acceso y Movimientos (E/S)</h3>
<p>Esta es una funcionalidad clave para la seguridad y el registro de asistencia del personal.</p>
<ul>
  <li><strong>Página</strong>: <code>pages/gestion_es_staff.php</code></li>
  <li><strong>Archivos Involucrados</strong>:</li>
  <li><code>public/js/gestion_es_staff.js</code> (Lógica del Frontend).</li>
  <li><code>api/registrar_movimiento_staff.php</code> (API para guardar la entrada/salida).</li>
  <li><code>api/consultar_movimiento_staff.php</code> (API para verificar el último estado de un miembro).</li>
  <li><strong>Flujo de Operación</strong>:</li>
  <li>1.  <strong>Interfaz de Registro</strong>: La página presenta una interfaz simple, probablemente con una cámara para escanear códigos QR o un campo para introducir la cédula.</li>
  <li>2.  <strong>Escaneo/Entrada de Cédula</strong>: El administrador escanea el QR del miembro del staff o introduce su cédula.</li>
  <li>3.  <strong>Verificación de Estado (AJAX)</strong>: <code>gestion_es_staff.js</code> envía la cédula a <code>api/consultar_movimiento_staff.php</code>. Esta API revisa la tabla <code>movimientos_staff</code> para determinar si el último movimiento registrado fue una entrada o una salida.</li>
  <li>4.  <strong>Registro de Movimiento (AJAX)</strong>: Basado en el estado actual, el sistema propone la acción contraria (si está "Adentro", propone "Registrar Salida" y viceversa). Al confirmar, <code>gestion_es_staff.js</code> llama a <code>api/registrar_movimiento_staff.php</code> para insertar el nuevo evento (entrada o salida) en la base de datos con la fecha y hora actual.</li>
  <li>5.  <strong>Feedback Visual</strong>: La interfaz se actualiza en tiempo real para mostrar el resultado de la operación y el estado actual del miembro del personal.</li>
</ul>
<h3>d. Generación de Códigos QR</h3>
<ul>
  <li><strong>Página/Reporte</strong>: <code>src/reports_generators/generar_qr_staff_pdf.php</code></li>
  <li><strong>Propósito</strong>: Generar un documento PDF que contiene el código QR de un miembro del staff, listo para ser impreso y utilizado como identificación.</li>
  <li><strong>Lógica</strong>: Este script toma la cédula de un miembro del personal, utiliza la librería <code>phpqrcode</code> para generar la imagen del código QR y luego usa la librería <code>FPDF</code> para incrustar esa imagen en un archivo PDF con un formato predefinido.</li>
</ul>
<h2>3. Reportes Asociados al Módulo</h2>
<p>El módulo de Staff está vinculado a varios reportes importantes que se generan desde el <strong>Módulo de Reportes</strong>.</p>
<ul>
  <li><code>generar_lista_staff_admin_PDF.php</code>: Genera un listado en PDF del personal administrativo.</li>
  <li><code>generar_lista_staff_docente_PDF.php</code>: Genera un listado en PDF del personal docente.</li>
  <li><code>generar_lista_staff_mantenimiento_PDF.php</code>: Genera un listado en PDF del personal de mantenimiento.</li>
  <li><code>pdf_movimiento_staff.php</code>: Genera un reporte detallado de los movimientos (entradas y salidas) del personal en un rango de fechas específico.</li>
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