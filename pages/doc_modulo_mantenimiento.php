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
<h1>Análisis de Funcionalidad: Módulo de Mantenimiento</h1>
<p>Este documento describe el flujo de trabajo y los componentes técnicos del "Módulo de Mantenimiento", una sección crítica para la configuración global y la seguridad de los datos del sistema.</p>
<hr>
<h3>Arquitectura General</h3>
<p>El módulo sigue una arquitectura clásica de PHP, donde las acciones se procesan en el backend a través del envío de formularios y recargas de página. El acceso a todas sus funcionalidades está correctamente restringido al rol de <code>master</code>, asegurando que solo los administradores con los más altos privilegios puedan realizar cambios en la configuración y los datos del sistema.</p>
<hr>
<h3>1. Gestión de Períodos Escolares (<code>periodos_escolares.php</code>)</h3>
<ul>
<li><strong>Funcionalidad:</strong> Permite al usuario <code>master</code> crear, activar y desactivar los períodos escolares que rigen el funcionamiento de todo el sistema.</li>
<li><strong>Lógica de Negocio Clave:</strong>
<ul>
<li>Solo se puede crear un nuevo período si no hay otro activo, previniendo inconsistencias.</li>
<li>La activación de un período se realiza dentro de una <strong>transacción de base de datos</strong> para garantizar que solo un período pueda estar activo a la vez.</li>
</ul>
</li>
<li><strong>Componentes Técnicos:</strong> Es una página PHP auto-contenida que procesa sus propios formularios (<code>POST</code>).</li>
</ul>
<hr>
<h3>2. Gestión de Usuarios del Sistema (<code>configurar_usuarios.php</code> y asociados)</h3>
<ul>
<li><strong>Funcionalidad:</strong> Permite al usuario <code>master</code> gestionar las cuentas de usuario (<code>master</code>, <code>admin</code>, <code>consulta</code>).</li>
<li><strong>Lógica de Negocio Clave:</strong>
<ul>
<li>Permite vincular cuentas a miembros del personal existentes.</li>
<li>Las contraseñas se encriptan de forma segura con <code>password_hash()</code>.</li>
<li>Un usuario no puede eliminarse a sí mismo.</li>
</ul>
</li>
<li><strong>Componentes Técnicos:</strong> Utiliza un flujo de trabajo de múltiples páginas (<code>configurar</code>, <code>editar</code>, <code>eliminar</code>) para separar las responsabilidades.</li>
</ul>
<hr>
<h3>3. Gestión de Backups (<code>backup_db.php</code>)</h3>
<ul>
<li><strong>Funcionalidad:</strong> Proporciona una interfaz para crear respaldos manuales de la base de datos y para descargar respaldos existentes.</li>
<li><strong>Flujo de Trabajo y Lógica de Negocio:</strong>
<ol>
<li><strong>Creación:</strong> Un botón "Realizar Respaldo Ahora" ejecuta un script PHP que invoca la utilidad de línea de comandos <code>pg_dump</code> del sistema. Esto crea un volcado completo de la base de datos en un archivo <code>.sql</code> con fecha y hora en el nombre, guardándolo en la carpeta <code>/PostgreSQL-DB/</code>.</li>
<li><strong>Listado y Descarga:</strong> La página escanea el directorio de respaldos y muestra una lista de los archivos existentes. Cada archivo tiene un enlace de descarga que, de forma segura, fuerza la descarga del archivo de respaldo solicitado en el navegador del usuario.</li>
</ol>
</li>
<li><strong>Componentes Técnicos:</strong>
<ul>
<li>Utiliza <code>exec()</code> de PHP para interactuar con la utilidad <code>pg_dump</code> de PostgreSQL.</li>
<li>Manipula cabeceras HTTP para gestionar las descargas de archivos de forma segura.</li>
</ul>
</li>
</ul>
<hr>
<h3>Conclusión General del Módulo</h3>
<p>El "Módulo de Mantenimiento" es una sección crítica, bien protegida y con una lógica de negocio sólida.</p>
<ul>
<li><strong>Fortalezas:</strong>
<ul>
<li><strong>Seguridad:</strong> El acceso está correctamente restringido por rol y las contraseñas y descargas se manejan de forma segura.</li>
<li><strong>Integridad de Datos:</strong> Las reglas de negocio, como las transacciones en la activación de períodos y el uso de <code>pg_dump</code> para respaldos consistentes, son puntos muy fuertes.</li>
<li><strong>Flujo de Trabajo Claro:</strong> La separación de las funciones en diferentes scripts hace que la lógica sea fácil de seguir y mantener.</li>
</ul>
</li>
</ul>
HTML;
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>