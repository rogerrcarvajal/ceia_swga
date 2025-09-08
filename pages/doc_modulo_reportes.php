<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/lib/Parsedown.php';
$Parsedown = new Parsedown();
$page_title = "Documentación Técnica: Módulo Reportes";

$markdown_content = <<<'MARKDOWN'
# Análisis de Funcionalidad: Módulo de Reportes

Este documento describe el flujo de trabajo y los componentes técnicos del "Módulo de Reportes", cuya función principal es la extracción de datos y su presentación en formato PDF.

---

### Arquitectura General

A diferencia de otros módulos interactivos del sistema, el Módulo de Reportes sigue una arquitectura clásica basada en PHP. La lógica principal reside en el backend, que se encarga de consultar la base de datos y renderizar la información. El uso de JavaScript es mínimo y se limita a mejorar la usabilidad de la interfaz, sin realizar llamadas a APIs para la obtención de datos.

---

### 1. Generar Planilla de Inscripción (`seleccionar_planilla.php`)

-   **Funcionalidad:** Permite al administrador generar un PDF con la planilla de inscripción completa de un estudiante específico.
-   **Flujo de Trabajo:**
    1.  La página carga una lista desplegable con todos los estudiantes inscritos en el período escolar activo.
    2.  El administrador selecciona un estudiante.
    3.  Al hacer clic en "Generar PDF", el ID del estudiante seleccionado se envía mediante un formulario GET al script `src/reports_generators/generar_planilla_pdf.php`.
-   **Lógica Técnica:** El script generador de PDF (inferido) recibe el ID, consulta toda la información asociada al estudiante (datos personales, padres, ficha médica) y la maqueta en un documento PDF detallado.

---

### 2. Roster Actualizado (`roster_actual.php`)

-   **Funcionalidad:** Ofrece una vista consolidada de todo el personal y los estudiantes activos en el período actual, con la opción de exportar esta vista a PDF.
-   **Flujo de Trabajo:**
    1.  Al cargar la página, el backend consulta y muestra en dos tablas separadas las listas de "Staff" y "Estudiantes" activos.
    2.  Un botón "Generar PDF del Roster" envía un formulario (sin necesidad de parámetros adicionales) al script `src/reports_generators/generar_roster_pdf.php`.
-   **Lógica Técnica:** El script generador de PDF replica la misma consulta de la página principal para obtener las listas de personal y estudiantes activos y las formatea en un documento PDF.

---

### 3. Gestionar Reportes (`gestionar_reportes.php`)

-   **Funcionalidad:** Serve como un panel centralizado para previsualizar y generar múltiples reportes de listas categorizadas.
-   **Flujo de Trabajo:**
    1.  **Carga Inicial:** Al cargar la página, el backend ejecuta todas las consultas necesarias para cada categoría de reporte (Estudiantes, Staff Administrativo, Staff Docente, etc.) y las renderiza en tablas HTML que permanecen ocultas inicialmente.
    2.  **Interacción del Usuario:** El usuario hace clic en una categoría en el menú lateral (ej. "Vehículos Autorizados").
    3.  **Visualización:** Un script de JavaScript simple se activa para mostrar la sección de vista previa correspondiente a la categoría seleccionada.
    4.  **Generación de PDF:** Cada sección de vista previa tiene su propio botón "Generar PDF", que apunta a un script generador de PDF específico para esa categoría (ej. `generar_lista_vehiculos_autorizados_PDF.php`).
-   **Lógica Técnica:** Este enfoque precarga todos los datos, haciendo que la experiencia de usuario para cambiar entre vistas previas sea instantánea. La modularidad es alta, ya que cada reporte tiene su propio script generador de PDF, facilitando el mantenimiento.

---

### Conclusión General del Módulo

El "Módulo de Reportes" es robusto y cumple su propósito de manera efectiva y directa. Su arquitectura basada en PHP es adecuada para la tarea de generar vistas de datos estáticas.

-   **Fortalezas:**
    -   **Claridad y Sencillez:** La lógica es directa y fácil de mantener.
    -   **Modularidad:** El uso de un script generador de PDF dedicado para cada reporte es una excelente práctica de diseño que aísla la lógica de cada uno.
    -   **Eficiencia:** En `gestionar_reportes.php`, la precarga de datos permite una navegación fluida entre las diferentes vistas previas sin esperas adicionales.

---

### 3. Reportes Asociados al Módulo

El módulo de Staff está vinculado a varios reportes importantes que se generan desde el Módulo de Reportes.

- **`generar_lista_staff_admin_PDF.php`**: Genera un listado en PDF del personal administrativo.
- **`generar_lista_staff_docente_PDF.php`**: Genera un listado en PDF del personal docente.
- **`generar_lista_staff_mantenimiento_PDF.php`**: Genera un listado en PDF del personal de mantenimiento.
- **`pdf_movimiento_staff.php`**: Genera un reporte detallado de los movimientos (entradas y salidas) del personal en un rango de fechas específico.

Estos scripts consultan las tablas `staff` y `movimientos_staff` y utilizan la librería `FPDF` para formatear y presentar los datos en documentos PDF.
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