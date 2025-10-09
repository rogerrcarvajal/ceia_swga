<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

//Declaracion de variables
$mensaje = "";

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master', 'consulta'])) {
    $_SESSION['error_acceso'] = "Acceso deneg ado. Solo usuarios autorizados tienen acceso a éste módulo.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}


// --- BLOQUE DE VERIFICACIÓN DE PERÍODO ESCOLAR ACTIVO ---
// --- Obtener el período escolar activo ---
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo. Es necesario activar uno para poder asignar personal.";
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
        .content img { width: 250px; }
        .document-container { background-color: rgba(0, 0, 0, 0.3); color: white; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); margin: 20px auto; padding: 20px 40px; border-radius: 10px; max-width: 80%; text-align: left; }
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
<h1>Manual de Usuario del Sistema Web de Gestión Académica (SWGA)</h1>
<h2>Introducción</h2>
<p>Bienvenido al Manual de Usuario del SWGA. Este documento está diseñado para guiar a los administradores y usuarios a través de las funcionalidades del sistema, asegurando un manejo eficiente y correcto de la plataforma.</p>
<hr>
<h2>Navegación Principal</h2>
<p>Tras iniciar sesión, el <strong>Dashboard</strong> (<code>dashboard.php</code>) actúa como el centro de mando, presentando un menú principal con acceso a todos los módulos del sistema. El acceso a cada módulo está determinado por el rol del usuario (<code>master</code>, <code>admin</code>, <code>consulta</code>).</p>
<hr>
<h2>Módulo Estudiantes</h2>
<p>Gestiona el ciclo de vida completo de la información de los estudiantes.</p>
<ol>
  <li><strong>Registrar Planilla de Inscripción</strong> (<code>planilla_inscripcion.php</code>): Se crean los nuevos estudiantes. El sistema permite buscar y vincular representantes (padres/madres) existentes para evitar duplicar información.</li>
  <li><strong>Gestionar Expedientes</strong> (<code>administrar_planilla_estudiantes.php</code>): Permite buscar a cualquier estudiante y modificar en detalle sus datos personales, la información de sus padres y su ficha médica a través de una interfaz dinámica.</li>
  <li><strong>Asignar a Período Escolar</strong> (<code>asignar_estudiante_periodo.php</code>): Vincula a un estudiante ya registrado con el período escolar activo y le asigna el grado que va a cursar. Este paso es crucial para que el estudiante aparezca en los rosters y reportes.</li>
  <li><strong>Autorización de Salida de Estudiantes</strong>:
      <ul>
          <li><strong>Generar Planilla de Salida</strong> (<code>planilla_salida.php</code>): Permite registrar una nueva autorización de salida para un estudiante, especificando quién lo retira (padre, madre u otro) y el motivo. Una vez guardada, se puede generar un comprobante en PDF.</li>
          <li><strong>Gestionar Planillas de Salida</strong> (<code>gestion_planilla_salida.php</code>): Ofrece una vista para buscar y consultar el historial de autorizaciones de salida, con filtros por semana y estudiante.</li>
      </ul>
  </li>
</ol>
<hr>
<h2>Módulo Staff</h2>
<p>Administra al personal de la institución (docentes, administrativos, etc.).</p>
<ol>
  <li><strong>Registrar y Gestionar Personal</strong> (<code>profesores_registro.php</code>): Esta pantalla central permite registrar nuevos miembros del personal y muestra una lista de todos los existentes.</li>
  <li><strong>Asignar a Período y Editar</strong> (<code>gestionar_profesor.php</code>): Desde la lista anterior, se puede acceder a esta pantalla para editar los datos de un miembro del personal y, fundamentalmente, para asignarlo a un puesto y al período escolar activo.</li>
  <li><strong>Autorización de Salida de Personal</strong>:
      <ul>
          <li><strong>Generar Planilla de Salida</strong> (<code>planilla_salida_staff.php</code>): Permite registrar un permiso de salida para un miembro del personal, detallando el motivo, fecha, hora y duración. Al guardar, se puede generar el PDF del permiso.</li>
          <li><strong>Gestionar Autorizaciones de Salida</strong> (<code>gestion_autorizacion_staff.php</code>): Permite consultar el historial de permisos de salida del personal, con filtros por semana, categoría y empleado.</li>
      </ul>
  </li>
</ol>
<hr>
<h2>Módulo Late-Pass y Control de Acceso</h2>
<p>Este es el módulo más dinámico, diseñado para el control de acceso en tiempo real mediante códigos QR.</p>
<ol>
  <li><strong>Generar Códigos QR</strong> (<code>generar_qr.php</code>): Permite generar carnets en PDF con códigos QR únicos para Estudiantes (con prefijo <code>EST-</code>), Personal (<code>STF-</code>) y Vehículos autorizados (<code>VEH-</code>).</li>
  <li><strong>Control de Acceso</strong> (<code>control_acceso.php</code>): Es la pantalla de escaneo. Utilizando un lector de QR de hardware, el sistema registra los movimientos:
      <ul>
          <li><strong>Estudiantes</strong>: Registra la llegada. Si es después de las 08:06:00, se contabiliza como un "strike" de tardanza para la semana.</li>
          <li><strong>Personal</strong>: Registra un ciclo de trabajo diario (una entrada y una salida).</li>
          <li><strong>Vehículos</strong>: Registra un ciclo simple de entrada y salida.</li>
      </ul>
  </li>
  <li><strong>Consultar Registros</strong>: Las páginas <code>gestion_latepass.php</code> (estudiantes), <code>gestion_es_staff.php</code> (personal) y <code>gestion_vehiculos.php</code> (vehículos) permiten consultar el historial de movimientos, filtrando por semana y por individuo, y exportar los resultados a PDF.</li>
</ol>
<hr>
<h2>Módulo de Reportes</h2>
<p>Centraliza la exportación de información clave del sistema en formato PDF.</p>
<ul>
  <li><strong>Planillas Individuales</strong> (<code>seleccionar_planilla.php</code>): Genera el PDF de la planilla de inscripción completa de un estudiante seleccionado.</li>
  <li><strong>Roster del Período</strong> (<code>roster_actual.php</code>): Genera un PDF con las listas completas de todo el personal y todos los estudiantes activos en el período actual.</li>
  <li><strong>Listas Personalizadas</strong> (<code>gestionar_reportes.php</code>): Ofrece vistas previas y permite generar PDFs de listas específicas, como "Staff Docente", "Vehículos Autorizados", etc.</li>
</ul>
<hr>
<h2>Módulo de Mantenimiento (Solo rol <code>master</code>)</h2>
<p>Contiene herramientas críticas para la administración del sistema.</p>
<ul>
  <li><strong>Períodos Escolares</strong> (<code>periodos_escolares.php</code>): Permite crear nuevos períodos y, crucialmente, activar el que corresponda al ciclo académico actual. Solo se puede activar un período a la vez.</li>
  <li><strong>Gestión de Usuarios</strong> (<code>configurar_usuarios.php</code>): Se utiliza para crear/editar/eliminar las cuentas de usuario (y sus roles) que pueden acceder al sistema.</li>
  <li><strong>Respaldo de Base de Datos</strong> (<code>backup_db.php</code>): Ofrece la funcionalidad para crear respaldos manuales de la base de datos y descargar respaldos existentes.</li>
</ul>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>
