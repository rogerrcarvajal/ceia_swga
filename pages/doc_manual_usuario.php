<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$page_title = "Manual de Usuario";
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
        <h1>Manual de Usuario del Sistema Web de Gestión Académica (SWGA)</h1>
<h2>Introducción</h2>
<p>Bienvenido al Manual de Usuario del SWGA. Este documento está diseñado para guiar a los administradores y usuarios del sistema a través de sus diversas funcionalidades, asegurando un manejo eficiente y correcto de la plataforma.</p>
<hr>
<h2>Módulo de Control de Acceso</h2>
<p>Este módulo es el punto de entrada al sistema y gestiona la seguridad.</p>
<ul>
  <li><strong>Login</strong>: Los usuarios acceden utilizando su cédula y contraseña.</li>
  <li><strong>Roles</strong>: El sistema reconoce diferentes roles (ej. master, admin) que determinan el acceso a los distintos módulos.</li>
  <li><strong>Dashboard</strong>: Una vez dentro, el dashboard (<code>dashboard.php</code>) presenta el menú principal, que es la puerta de entrada a todas las demás secciones.</li>
</ul>
<hr>
<h2>Módulo de Estudiantes</h2>
<p>Permite la gestión integral de la información de los estudiantes.</p>
<ul>
  <li><strong>Inscripción</strong>: A través de <code>planilla_inscripcion.php</code>, se registran nuevos estudiantes junto con los datos de sus padres y su ficha médica.</li>
  <li><strong>Gestión de Expedientes</strong>: En <code>administrar_planilla_estudiantes.php</code>, se puede buscar, visualizar y <strong>actualizar</strong> la información de cualquier estudiante, incluyendo datos personales, de los padres y médicos, todo de forma asíncrona gracias a AJAX.</li>
  <li><strong>Asignación a Períodos</strong>: En <code>gestionar_estudiantes.php</code>, se vincula a los estudiantes con el período escolar activo y se les asigna un grado.</li>
</ul>
<hr>
<h2>Módulo de Staff</h2>
<p>Administra al personal de la institución (docentes, administrativos, etc.).</p>
<ul>
  <li><strong>Registro</strong>: <code>profesores_registro.php</code> permite crear nuevos perfiles para el personal.</li>
  <li><strong>Gestión</strong>: <code>gestionar_profesor.php</code> ofrece una lista del personal para editar su información o eliminar registros.</li>
  <li><strong>Control de Entradas/Salidas</strong>: <code>gestion_es_staff.php</code> registra los movimientos del personal mediante la lectura de su código QR o cédula, actualizando su estado (dentro o fuera del plantel).</li>
</ul>
<hr>
<h2>Módulo de Late-Pass (Pases de Llegada Tarde)</h2>
<p>Diseñado para el control de retardos de los estudiantes.</p>
<ul>
  <li><strong>Generación de Pase</strong>: En <code>gestion_latepass.php</code>, se busca al estudiante por su cédula, se registra el retardo en la base de datos y se genera un pase en formato PDF listo para imprimir.</li>
</ul>
<hr>
<h2>Módulo de Reportes</h2>
<p>Centraliza la exportación de información clave del sistema.</p>
<ul>
  <li><strong>Generación de Reportes</strong>: Desde <code>gestionar_reportes.php</code>, los administradores pueden generar una variedad de listados en PDF, tales como:</li>
  <li>Listas de estudiantes por grado.</li>
  <li>Listas de personal por departamento.</li>
  <li>Historial de movimientos de personal y vehículos.</li>
  <li>Planillas de inscripción individuales.</li>
  <li>Carnets con códigos QR para estudiantes, personal y vehículos.</li>
</ul>
<hr>
<h2>Módulo de Mantenimiento</h2>
<p>Contiene herramientas críticas para la administración del sistema, accesibles solo para el usuario 'master'.</p>
<ul>
  <li><strong>Períodos Escolares</strong>: <code>periodos_escolares.php</code> permite crear nuevos períodos y activar el que corresponda al ciclo académico actual.</li>
  <li><strong>Gestión de Usuarios</strong>: <code>configurar_usuarios.php</code> se utiliza para crear las cuentas de usuario que podrán acceder al sistema, vinculándolas a un miembro del staff.</li>
  <li><strong>Respaldo de Base de Datos</strong>: <code>backup_db.php</code> ofrece la funcionalidad para crear respaldos manuales de la base de datos y descargar respaldos existentes.</li>
</ul>

HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>
