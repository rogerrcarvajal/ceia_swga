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
$page_title = "Documentación del Módulo de Mantenimiento";
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
        <h1>Documentación del Módulo de Mantenimiento</h1>
<h2>1. Propósito del Módulo</h2>
<p>El módulo de Mantenimiento centraliza las tareas administrativas críticas para la salud y seguridad del sistema SWGA. Actualmente, su principal funcionalidad es la gestión de respaldos de la base de datos, pero está diseñado para poder incorporar futuras herramientas de administración del sistema.</p>
<p>El acceso a este módulo está estrictamente restringido al rol de usuario <strong>'master'</strong>.</p>
<h2>2. Funcionalidad: Respaldo de Base de Datos (<code>backup_db.php</code>)</h2>
<p>Esta sección permite la creación de respaldos manuales y la visualización y descarga de todos los respaldos existentes.</p>
<h3>a. Lógica de Negocio y Flujo de Operación</h3>
<ol>
  <li><strong>Respaldo Manual</strong>: Un usuario 'master' puede generar un respaldo instantáneo de la base de datos <code>ceia_db</code> presionando el botón "Realizar Respaldo Ahora". El proceso técnico es el mismo que se describió en el documento anterior (<code>Funcionalidad_Modulo_Backup.md</code>): se invoca al programa <code>pg_dump</code> del servidor de forma segura para generar un archivo <code>.sql</code> con la fecha y hora actual en su nombre.</li>
  <li><strong>Historial de Respaldos</strong>: Esta nueva sección muestra una lista de todos los archivos de respaldo (<code>.sql</code>) que se encuentran en el directorio <code>PostgreSQL-DB/</code>. La lista se presenta en orden cronológico descendente (los más recientes primero), permitiendo al administrador ver el historial completo de respaldos, tanto los generados manualmente como los automáticos.</li>
  <li><strong>Descarga de Respaldos</strong>: Junto a cada archivo en el historial, hay un botón "Descargar". Al hacer clic, el navegador inicia la descarga del archivo <code>.sql</code> seleccionado. Esto se logra a través de un gestor de descargas seguro implementado en el mismo archivo <code>backup_db.php</code>, que verifica que el archivo solicitado sea un respaldo válido antes de enviarlo al usuario.</li>
</ol>
<h3>b. Configuración de Respaldos Automáticos (Tarea Programada en Windows)</h3>
<p>Como se mencionó, la automatización de respaldos no se gestiona desde la página web, sino desde el sistema operativo del servidor. A continuación se detalla cómo configurar un respaldo diario automático en Windows utilizando el <strong>Programador de Tareas</strong>.</p>
<p><strong>Objetivo</strong>: Ejecutar el comando de respaldo de la base de datos todos los días a una hora específica (por ejemplo, a las 11:00 PM).</p>
<p><strong>Paso 1: Crear un script de respaldo</strong></p>
<p>Es más robusto y seguro ejecutar la tarea desde un archivo de script en lugar de un comando directo. Crea un archivo llamado <code>backup_script.bat</code> en una ubicación segura en tu servidor (ej. <code>C:\Scripts\</code>).</p>
<p>El contenido del archivo <code>backup_script.bat</code> debe ser el siguiente. Este script hace dos cosas: establece la contraseña de la base de datos como una variable de entorno local (solo para este script) y luego ejecuta el comando <code>pg_dump</code>.</p>
<pre><code>@echo off
REM Script para realizar el respaldo diario de la base de datos ceia_db

REM --- CONFIGURACIÓN ---
REM Ruta al ejecutable pg_dump.exe
set PG_DUMP_PATH="C:\Program Files\PostgreSQL\17\bin\pg_dump.exe"

REM Credenciales de la base de datos (ajustar si es necesario)
set PG_HOST=localhost
set PG_USER=postgres
set PGPASSWORD=4674
set DB_NAME=ceia_db

REM Directorio de destino para los respaldos
set BACKUP_DIR="c:\xampp\htdocs\ceia_swga\PostgreSQL-DB"

REM Nombre del archivo de respaldo con fecha
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /format:list') do set datetime=%%I
set BACKUP_FILENAME=%BACKUP_DIR%\ceia_db_backup_%datetime:~0,8%_%datetime:~8,6%.sql

REM --- EJECUCIÓN DEL RESPALDO ---
echo Realizando respaldo de %DB_NAME%...
%PG_DUMP_PATH% -U %PG_USER% -h %PG_HOST% -d %DB_NAME% -F p -E UTF-8 -f %BACKUP_FILENAME%

echo Respaldo completado: %BACKUP_FILENAME%</code></pre>
<p><strong>Paso 2: Configurar la Tarea Programada en Windows</strong></p>
<ol>
  <li>Abre el <strong>Programador de Tareas</strong> (puedes buscarlo en el menú de inicio de Windows).</li>
  <li>En el panel de la derecha, haz clic en <strong>"Crear tarea básica..."</strong>.</li>
  <li><strong>Nombre</strong>: Escribe un nombre descriptivo, como "Respaldo Diario BD CEIA".</li>
  <li><strong>Desencadenador</strong>: Selecciona <strong>"Diariamente"</strong> y establece la hora a la que quieres que se ejecute (ej. 11:00:00 PM).</li>
  <li><strong>Acción</strong>: Selecciona <strong>"Iniciar un programa"</strong>.</li>
  <li><strong>Programa/script</strong>: Haz clic en <strong>"Examinar..."</strong> y busca y selecciona el archivo <code>backup_script.bat</code> que creaste en el Paso 1.</li>
  <li><strong>Finalizar</strong>: Revisa la configuración y haz clic en <strong>"Finalizar"</strong>.</li>
</ol>
<p>Con estos pasos, Windows ejecutará automáticamente el script de respaldo todos los días a la hora especificada, asegurando que siempre tengas una copia de seguridad reciente de tu base de datos. Estos respaldos automáticos aparecerán en el "Historial de Respaldos" de la aplicación web.</p>
<h2>3. Futuras Mejoras</h2>
<p>El módulo de mantenimiento podría expandirse para incluir:</p>
<ul>
  <li><strong>Restauración de Respaldos</strong>: Una interfaz para restaurar la base de datos a partir de un archivo de respaldo existente.</li>
  <li><strong>Logs del Sistema</strong>: Un visor para los logs de errores de PHP o de la aplicación.</li>
  <li><strong>Optimización de la Base de Datos</strong>: Herramientas para ejecutar comandos de mantenimiento de PostgreSQL como <code>VACUUM</code> o <code>REINDEX</code>.</li>
</ul>

HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>