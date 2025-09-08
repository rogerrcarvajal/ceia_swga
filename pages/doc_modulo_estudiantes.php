<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/lib/Parsedown.php';
$Parsedown = new Parsedown();
$page_title = "Documentación Técnica: Módulo Estudiantes";

$markdown_content = <<<'MARKDOWN'
# Análisis de Funcionalidad y Lógica: Módulo Estudiante

Este documento detalla el análisis del flujo de trabajo, las interacciones de frontend/backend y la lógica de negocio para el Módulo de Estudiantes.

## Componentes Principales

-   **`pages/planilla_inscripcion.php`**: Formulario para el ingreso de nuevos estudiantes.
-   **`pages/administrar_planilla_estudiantes.php`**: Panel para la consulta y modificación de expedientes de estudiantes existentes.
-   **`pages/asignar_estudiante_periodo.php`**: Panel para vincular estudiantes a un período escolar y asignarles un grado.

---

### 1. `planilla_inscripcion.php` (Creación de Expedientes)

Este componente gestiona el registro inicial de un estudiante, incluyendo sus datos personales, la información de sus padres y la ficha médica.

#### Flujo de Usuario

1.  El administrador completa los datos del estudiante.
2.  Al llegar a la sección "Datos del Padre/Madre", introduce el número de cédula o pasaporte.
3.  El sistema, de forma automática, busca si el representante ya existe en la base de datos.
4.  **Si el representante existe:** La interfaz muestra la opción **"Vincular"**. Al seleccionarla, los campos de datos de esa persona se bloquean y el sistema se prepara para usar el ID del registro existente.
5.  **Si el representante no existe:** El administrador procede a llenar todos los campos para crear un nuevo registro para el padre/madre.
6.  Al hacer clic en "Guardar Planilla", el backend procesa la solicitud en una transacción segura.

#### Componentes Técnicos

*   **Frontend:** JavaScript integrado (`inline`) en el propio archivo `.php`.
*   **API Involucrada:** `GET /api/buscar_representante.php` para verificar la existencia de los padres/madres.
*   **Lógica de Backend:** El script PHP gestiona todo el proceso como una **transacción de base de datos**, garantizando la integridad de los datos.

---

### 2. `administrar_planilla_estudiantes.php` (Gestión de Expedientes)

Este panel permite la visualización y actualización de la información de cualquier estudiante registrado en el sistema.

#### Flujo de Usuario

1.  El administrador visualiza una lista completa de estudiantes y selecciona uno.
2.  El sistema carga dinámicamente su expediente completo en cuatro formularios independientes (Estudiante, Padre, Madre, Ficha Médica).
3.  El administrador puede modificar y guardar los cambios de forma individual para cada sección.

#### Componentes Técnicos

*   **Frontend:** La lógica reside en el archivo externo `/public/js/admin_estudiantes.js`.
*   **APIs de Lectura (GET):** Se usan múltiples APIs para obtener los datos por separado: `obtener_estudiante.php`, `obtener_padre.php`, `obtener_madre.php`, `obtener_ficha_medica.php`.
*   **APIs de Escritura (POST):** Cada formulario tiene su propio endpoint para las actualizaciones: `actualizar_estudiante.php`, `actualizar_padre.php`, etc.

---

### 3. `asignar_estudiante_periodo.php` (Asignación a Período)

Esta funcionalidad es el puente entre el registro de un estudiante y su participación activa en la vida académica.

#### Flujo de Usuario

1.  El administrador selecciona un período escolar de una lista.
2.  La interfaz se actualiza dinámicamente, mostrando dos listas:
    *   A la izquierda, los estudiantes **ya asignados** a ese período.
    *   A la derecha, en un formulario, los estudiantes **disponibles para asignar** (aquellos que no están en ningún período).
3.  El administrador elige un estudiante disponible, le asigna un grado y hace clic en "Asignar".
4.  La asignación se procesa en segundo plano, y las listas se actualizan automáticamente sin recargar la página.

#### Componentes Técnicos

*   **Frontend:** La lógica reside en `/public/js/admin_asignar_estudiante.js` (inferido), que orquesta las llamadas a las APIs.
*   **APIs Involucradas:**
    *   `GET /api/obtener_estudiantes_asignados.php` (inferido): Para poblar la lista del panel izquierdo.
    *   `GET /api/obtener_estudiantes_no_asignados.php`: Para poblar el menú de estudiantes disponibles.
    *   `POST /api/asignar_estudiante.php`: Para crear el vínculo en la tabla `estudiante_periodo`, registrando la asignación.

---

### Conclusión sobre la Lógica de Negocio

*   **Relación 1-a-Muchos (Representantes):** La implementación de la relación "un representante a muchos estudiantes" es un punto fuerte del sistema. Se maneja de forma robusta tanto en la creación (evitando duplicados) como en la gestión (los cambios en un padre se reflejan en todos sus representados).
*   **Ciclo de Vida del Estudiante:** El módulo gestiona el ciclo de vida completo:
    1.  **Creación** (`planilla_inscripcion.php`).
    2.  **Asignación** a un período y grado (`asignar_estudiante_periodo.php`).
    3.  **Gestión y Actualización** continua (`administrar_planilla_estudiantes.php`).
*   **Arquitectura Moderna:** El módulo combina de forma efectiva páginas clásicas renderizadas por el servidor con paneles dinámicos que consumen APIs y se actualizan en tiempo real, ofreciendo una experiencia de usuario fluida y eficiente.
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