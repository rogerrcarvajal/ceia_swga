<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/lib/Parsedown.php';
$Parsedown = new Parsedown();
$page_title = "Documentación Técnica: Módulo Mantenimiento";

$markdown_content = <<<'MARKDOWN'
# Análisis de Funcionalidad: Módulo de Mantenimiento

Este documento describe el flujo de trabajo y los componentes técnicos del "Módulo de Mantenimiento", una sección crítica para la configuración global y la seguridad de los datos del sistema.

---

### Arquitectura General

El módulo sigue una arquitectura clásica de PHP, donde las acciones se procesan en el backend a través del envío de formularios y recargas de página. El acceso a todas sus funcionalidades está correctamente restringido al rol de `master`, asegurando que solo los administradores con los más altos privilegios puedan realizar cambios en la configuración y los datos del sistema.

---

### 1. Gestión de Períodos Escolares (`periodos_escolares.php`)

-   **Funcionalidad:** Permite al usuario `master` crear, activar y desactivar los períodos escolares que rigen el funcionamiento de todo el sistema.
-   **Lógica de Negocio Clave:**
    -   Solo se puede crear un nuevo período si no hay otro activo, previniendo inconsistencias.
    -   La activación de un período se realiza dentro de una **transacción de base de datos** para garantizar que solo un período pueda estar activo a la vez.
-   **Componentes Técnicos:** Es una página PHP auto-contenida que procesa sus propios formularios (`POST`).

---

### 2. Gestión de Usuarios del Sistema (`configurar_usuarios.php` y asociados)

-   **Funcionalidad:** Permite al usuario `master` gestionar las cuentas de usuario (`master`, `admin`, `consulta`).
-   **Lógica de Negocio Clave:**
    -   Permite vincular cuentas a miembros del personal existentes.
    -   Las contraseñas se encriptan de forma segura con `password_hash()`.
    -   Un usuario no puede eliminarse a sí mismo.
-   **Componentes Técnicos:** Utiliza un flujo de trabajo de múltiples páginas (`configurar`, `editar`, `eliminar`) para separar las responsabilidades.

---

### 3. Gestión de Backups (`backup_db.php`)

-   **Funcionalidad:** Proporciona una interfaz para crear respaldos manuales de la base de datos y para descargar respaldos existentes.
-   **Flujo de Trabajo y Lógica de Negocio:**
    1.  **Creación:** Un botón "Realizar Respaldo Ahora" ejecuta un script PHP que invoca la utilidad de línea de comandos `pg_dump` del sistema. Esto crea un volcado completo de la base de datos en un archivo `.sql` con fecha y hora en el nombre, guardándolo en la carpeta `/PostgreSQL-DB/`.
    2.  **Listado y Descarga:** La página escanea el directorio de respaldos y muestra una lista de los archivos existentes. Cada archivo tiene un enlace de descarga que, de forma segura, fuerza la descarga del archivo de respaldo solicitado en el navegador del usuario.
-   **Componentes Técnicos:**
    -   Utiliza `exec()` de PHP para interactuar con la utilidad `pg_dump` de PostgreSQL.
    -   Manipula cabeceras HTTP para gestionar las descargas de archivos de forma segura.

---

### Conclusión General del Módulo

El "Módulo de Mantenimiento" es una sección crítica, bien protegida y con una lógica de negocio sólida.

-   **Fortalezas:**
    -   **Seguridad:** El acceso está correctamente restringido por rol y las contraseñas y descargas se manejan de forma segura.
    -   **Integridad de Datos:** Las reglas de negocio, como las transacciones en la activación de períodos y el uso de `pg_dump` para respaldos consistentes, son puntos muy fuertes.
    -   **Flujo de Trabajo Claro:** La separación de las funciones en diferentes scripts hace que la lógica sea fácil de seguir y mantener.
MARKDOWN;


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
        <?php echo $Parsedown->text($markdown_content); ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="/ceia_swga/pages/menu_ayuda.php" class="btn-back">Volver al Menú de Ayuda</a>
        </div>
    </div>

</body>
</html>