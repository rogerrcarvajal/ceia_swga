<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/lib/Parsedown.php';
$Parsedown = new Parsedown();
$page_title = "Documentación Técnica: Módulo Late-Pass";

$markdown_content = <<<'MARKDOWN'
# Análisis Completo de Funcionalidad: Módulo Late-Pass

Este documento consolida el análisis exhaustivo de todos los componentes del Módulo Late-Pass, desde la generación de códigos QR hasta la consulta de registros. El módulo es una pieza central del sistema, caracterizado por una arquitectura consistente y una clara separación de responsabilidades.

---

## Parte 1: Generación de Códigos QR

Esta sección describe el flujo de trabajo para la creación de identificadores únicos para cada entidad del sistema.

### Componentes Principales

- **`pages/generar_qr.php`**: Interfaz central para seleccionar la entidad a la que se le generará un QR.
- **Scripts Generadores de PDF**:
    - `src/reports_generators/generar_qr_pdf.php` (para Estudiantes)
    - `src/reports_generators/generar_qr_staff_pdf.php` (para Personal)
    - `src/reports_generators/generar_qr_vehiculo_pdf.php` (para Vehículos)

### Flujo de Trabajo

1.  **Selección**: El administrador utiliza la interfaz de `generar_qr.php` para seleccionar una categoría (ej. "Estudiantes") y luego un individuo específico de una lista poblada dinámicamente.
2.  **Enrutamiento**: El JavaScript de la página asigna el `action` del formulario al script generador de PDF correcto según la categoría.
3.  **Generación**: El script PHP correspondiente recibe el ID, construye un string con un prefijo identificador (`EST-`, `STF-`, `VEH-`), genera una imagen de código QR con la librería `endroid/qrcode`, y la incrusta en un carnet de identificación simple usando `FPDF`.
4.  **Descarga**: El PDF resultante se envía al navegador para su descarga.

### Conclusión (Parte 1)

El sistema de generación es eficiente y robusto. El uso de prefijos en el contenido del QR es una decisión de diseño clave que simplifica drásticamente el procesamiento en el punto de escaneo.

---

## Parte 2: Control de Acceso (Escaneo de QR)

Esta es la funcionalidad principal del módulo, donde los códigos QR se utilizan para registrar movimientos en tiempo real.

### Componentes Principales

- **`pages/control_acceso.php`**: Interfaz de usuario para el escaneo.
- **`public/js/control_acceso.js`**: Lógica de cliente que procesa el escaneo.
- **APIs de Registro**: `registrar_llegada.php` (Estudiantes), `registrar_movimiento_staff.php` (Personal), `registrar_movimiento_vehiculo.php` (Vehículos).

### Flujo de Trabajo

1.  **Captura**: La página `control_acceso.php` mantiene el foco en un campo de texto, esperando la entrada de un lector de QR de hardware.
2.  **Procesamiento (JS)**: El script `control_acceso.js` captura la entrada, identifica el prefijo del código y determina a qué API debe enviar la solicitud.
3.  **Lógica de Negocio (API)**: Cada API de registro ejecuta reglas de negocio específicas:
    - **Estudiantes**: Siempre registra la llegada. Si es después de las 08:06:00, cuenta un "strike" de tardanza para la semana.
    - **Personal**: Gestiona un ciclo de trabajo diario (una entrada antes de las 12 PM, una salida después de las 12 PM).
    - **Vehículos**: Gestiona un ciclo simple de entrada/salida.
4.  **Feedback**: La API devuelve una respuesta JSON que el JavaScript utiliza para mostrar una tarjeta de información con un código de colores, informando al operador del resultado del escaneo.

### Conclusión (Parte 2)

El sistema de control de acceso es el núcleo funcional del módulo. Está diseñado para ser rápido y aplica reglas de negocio complejas y bien diferenciadas para cada tipo de entidad, garantizando la integridad de los datos.

---

## Parte 3, 4 y 5: Gestión y Consulta de Registros

Las tres secciones de consulta (Late-Pass de Estudiantes, Entradas/Salidas de Staff y Movimientos de Vehículos) siguen un patrón de diseño idéntico y consistente, lo que representa una de las mayores fortalezas del módulo.

### Componentes Comunes

- **Páginas de Interfaz**: `gestion_latepass.php`, `gestion_es_staff.php`, `gestion_vehiculos.php`.
- **Scripts de Lógica**: `gestion_latepass.js`, `gestion_es_staff.js`, `gestion_vehiculos.js`.
- **APIs de Consulta**: `consultar_latepass.php`, `consulta_movimientos_staff.php`, `consulta_movimientos_vehiculos.php`.

### Flujo de Trabajo Común

1.  **Interfaz**: Cada página ofrece un conjunto de filtros (siempre por semana, y luego por estudiante, personal o vehículo).
2.  **Lógica de Cliente (JS)**: Al cargar la página, el script establece la semana actual por defecto y carga los datos iniciales. Cada vez que un filtro cambia, se realiza una nueva petición `fetch` a la API correspondiente.
3.  **API de Datos**: La API recibe los filtros, construye una consulta SQL para obtener los datos relevantes de la base de datos y los devuelve en formato JSON.
4.  **Visualización**: El script de JavaScript procesa la respuesta JSON y actualiza dinámicamente el contenido de la tabla HTML sin necesidad de recargar la página.
5.  **Exportación a PDF**: Un botón permite abrir una nueva pestaña que apunta a un script generador de PDF, pasándole los mismos filtros para que el reporte impreso coincida con la vista en pantalla.

### Conclusión (Parte 3, 4 y 5)

Estos módulos de consulta están muy bien diseñados para la inteligencia de negocio. Permiten a los administradores filtrar y visualizar datos de manera eficiente. La consistencia en el diseño y la arquitectura facilita enormemente el mantenimiento y la escalabilidad del sistema.

---

## Conclusión Final del Módulo Late-Pass

El Módulo Late-Pass es una pieza de ingeniería de software sólida, bien planificada y ejecutada.

- **Fortalezas Clave**:
    - **Arquitectura Consistente**: El uso repetido del patrón (Interfaz de Filtros -> JS -> API -> Tabla Dinámica) en todas las secciones de consulta es ejemplar.
    - **Separación de Responsabilidades**: La división entre presentación (HTML), interacción (JS) y lógica (PHP/API) es clara y sigue las mejores prácticas.
    - **Experiencia de Usuario Fluida**: Las interfaces son dinámicas, responden rápidamente a las acciones del usuario y proporcionan feedback visual claro.
    - **Lógica de Negocio Robusta**: Las reglas para el registro de movimientos son específicas y están protegidas por transacciones de base de datos.

- **Punto Menor de Mejora Sugerido**:
    - Las APIs de consulta para Staff y Vehículos podrían mejorarse para manejar la opción de "Todos", que actualmente se ofrece en la interfaz pero no está implementada en el backend. Habilitar esta funcionalidad proporcionaría una visión general valiosa para los administradores.

---

### c. Control de Acceso y Movimientos (E/S)

Esta es una funcionalidad clave para la seguridad y el registro de asistencia del personal.

**Página:** `pages/gestion_es_staff.php`
**Archivos Involucrados:**
- `public/js/gestion_es_staff.js` (Lógica del Frontend).
- `api/registrar_movimiento_staff.php` (API para guardar la entrada/salida).
- `api/consultar_movimiento_staff.php` (API para verificar el último estado de un miembro).

**Flujo de Operación:**
1. **Interfaz de Registro:** La página presenta una interfaz simple, probablemente con una cámara para escanear códigos QR o un campo para introducir la cédula.
2. **Escaneo/Entrada de Cédula:** El administrador escanea el QR del miembro del staff o introduce su cédula.
3. **Verificación de Estado (AJAX):** `gestion_es_staff.js` envía la cédula a `api/consultar_movimiento_staff.php`. Esta API revisa la tabla `movimientos_staff` para determinar si el último movimiento registrado fue una entrada o una salida.
4. **Registro de Movimiento (AJAX):** Basado en el estado actual, el sistema propone la acción contraria (si está "Adentro", propone "Registrar Salida" y viceversa). Al confirmar, `gestion_es_staff.js` llama a `api/registrar_movimiento_staff.php` para insertar el nuevo evento (entrada o salida) en la base de datos con la fecha y hora actual.
5. **Feedback Visual:** La interfaz se actualiza en tiempo real para mostrar el resultado de la operación y el estado actual del miembro del personal.

### d. Generación de Códigos QR

**Página/Reporte:** `src/reports_generators/generar_qr_staff_pdf.php`
**Propósito:** Generar un documento PDF que contiene el código QR de un miembro del staff, listo para ser impreso y utilizado como identificación.
**Lógica:** Este script toma la cédula de un miembro del personal, utiliza la librería `phpqrcode` para generar la imagen del código QR y luego usa la librería `FPDF` para incrustar esa imagen en un archivo PDF con un formato predefinido.
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