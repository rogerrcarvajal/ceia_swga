<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$page_title = "Módulo de Late-Pass - Documentación Técnica del Sistema";
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
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="document-container">
        <?php echo <<<HTML
<h1>Análisis Completo de Funcionalidad: Módulo Late-Pass</h1>
<p>Este documento consolida el análisis exhaustivo de todos los componentes del Módulo Late-Pass, desde la generación de códigos QR hasta la consulta de registros. El módulo es una pieza central del sistema, caracterizado por una arquitectura consistente y una clara separación de responsabilidades.</p>
<hr>
<h2>Parte 1: Generación de Códigos QR</h2>
<p>Esta sección describe el flujo de trabajo para la creación de identificadores únicos para cada entidad del sistema.</p>
<h3>Componentes Principales</h3>
<ul>
<li><strong><code>pages/generar_qr.php</code></strong>: Interfaz central para seleccionar la entidad a la que se le generará un QR.</li>
<li><strong>Scripts Generadores de PDF</strong>:
<ul>
<li><code>src/reports_generators/generar_qr_pdf.php</code> (para Estudiantes)</li>
<li><code>src/reports_generators/generar_qr_staff_pdf.php</code> (para Personal)</li>
<li><code>src/reports_generators/generar_qr_vehiculo_pdf.php</code> (para Vehículos)</li>
</ul>
</li>
</ul>
<h3>Flujo de Trabajo</h3>
<ol>
<li><strong>Selección</strong>: El administrador utiliza la interfaz de <code>generar_qr.php</code> para seleccionar una categoría (ej. "Estudiantes") y luego un individuo específico de una lista poblada dinámicamente.</li>
<li><strong>Enrutamiento</strong>: El JavaScript de la página asigna el <code>action</code> del formulario al script generador de PDF correcto según la categoría.</li>
<li><strong>Generación</strong>: El script PHP correspondiente recibe el ID, construye un string con un prefijo identificador (<code>EST-</code>, <code>STF-</code>, <code>VEH-</code>), genera una imagen de código QR con la librería <code>endroid/qrcode</code>, y la incrusta en un carnet de identificación simple usando <code>FPDF</code>.</li>
<li><strong>Descarga</strong>: El PDF resultante se envía al navegador para su descarga.</li>
</ol>
<h3>Conclusión (Parte 1)</h3>
<p>El sistema de generación es eficiente y robusto. El uso de prefijos en el contenido del QR es una decisión de diseño clave que simplifica drásticamente el procesamiento en el punto de escaneo.</p>
<hr>
<h2>Parte 2: Control de Acceso (Escaneo de QR)</h2>
<p>Esta es la funcionalidad principal del módulo, donde los códigos QR se utilizan para registrar movimientos en tiempo real.</p>
<h3>Componentes Principales</h3>
<ul>
<li><strong><code>pages/control_acceso.php</code></strong>: Interfaz de usuario para el escaneo.</li>
<li><strong><code>public/js/control_acceso.js</code></strong>: Lógica de cliente que procesa el escaneo.</li>
<li><strong>APIs de Registro</strong>: <code>registrar_llegada.php</code> (Estudiantes), <code>registrar_movimiento_staff.php</code> (Personal), <code>registrar_movimiento_vehiculo.php</code> (Vehículos).</li>
</ul>
<h3>Flujo de Trabajo</h3>
<ol>
<li><strong>Captura</strong>: La página <code>control_acceso.php</code> mantiene el foco en un campo de texto, esperando la entrada de un lector de QR de hardware.</li>
<li><strong>Procesamiento (JS)</strong>: El script <code>control_acceso.js</code> captura la entrada, identifica el prefijo del código y determina a qué API debe enviar la solicitud.</li>
<li><strong>Lógica de Negocio (API)</strong>: Cada API de registro ejecuta reglas de negocio específicas:
<ul>
<li><strong>Estudiantes</strong>: Siempre registra la llegada. Si es después de las 08:05:59, cuenta un "strike" de tardanza para la semana. El sistema lleva un conteo de strikes semanales. Al alcanzar los 3 strikes, se muestra una observación especial indicando que el estudiante pierde la primera hora de clases y debe ser remitido a la administración.</li>
<li><strong>Personal</strong>: Gestiona un ciclo de trabajo diario (una entrada antes de las 12 PM, una salida después de las 12 PM).</li>
<li><strong>Vehículos</strong>: Gestiona un ciclo simple de entrada/salida.</li>
</ul>
<h3>Conclusión (Parte 2)</h3>
<p>El sistema de control de acceso es el núcleo funcional del módulo. Está diseñado para ser rápido y aplica reglas de negocio complejas y bien diferenciadas para cada tipo de entidad, garantizando la integridad de los datos.</p>
<hr>
<h2>Parte 3, 4 y 5: Gestión y Consulta de Registros</h2>
<p>Las tres secciones de consulta (Late-Pass de Estudiantes, Entradas/Salidas de Staff y Movimientos de Vehículos) siguen un patrón de diseño idéntico y consistente, lo que representa una de las mayores fortalezas del módulo.</p>
<h3>Componentes Comunes</h3>
<ul>
<li><strong>Páginas de Interfaz</strong>: <code>gestion_latepass.php</code>, <code>gestion_es_staff.php</code>, <code>gestion_vehiculos.php</code>.</li>
<li><strong>Scripts de Lógica</strong>: <code>gestion_latepass.js</code>, <code>gestion_es_staff.js</code>, <code>gestion_vehiculos.js</code>.</li>
<li><strong>APIs de Consulta</strong>: <code>consultar_latepass.php</code>, <code>consultar_movimiento_staff.php</code>, <code>consultar_movimiento_vehiculos.php</code>.</li>
</ul>
<h3>Flujo de Trabajo Común</h3>
<ol>
<li><strong>Interfaz</strong>: Cada página ofrece un conjunto de filtros (siempre por semana, y luego por estudiante, personal o vehículo).</li>
<li><strong>Lógica de Cliente (JS)</strong>: Al cargar la página, el script establece la semana actual por defecto y carga los datos iniciales. Cada vez que un filtro cambia, se realiza una nueva petición <code>fetch</code> a la API correspondiente.</li>
<li><strong>API de Datos</strong>: La API recibe los filtros, construye una consulta SQL para obtener los datos relevantes de la base de datos y los devuelve en formato JSON.</li>
<li><strong>Visualización</strong>: El script de JavaScript procesa la respuesta JSON y actualiza dinámicamente el contenido de la tabla HTML sin necesidad de recargar la página. En el caso de la gestión de Late-Pass, la tabla solo muestra los estudiantes que tienen al menos una llegada tarde registrada en la semana seleccionada.</li>
<li><strong>Exportación a PDF</strong>: Un botón permite abrir una nueva pestaña que apunta a un script generador de PDF, pasándole los mismos filtros para que el reporte impreso coincida con la vista en pantalla.</li>
</ol>
<h3>Conclusión (Parte 3, 4 y 5)</h3>
<p>Estos módulos de consulta están muy bien diseñados para la inteligencia de negocio. Permiten a los administradores filtrar y visualizar datos de manera eficiente. La consistencia en el diseño y la arquitectura facilita enormemente el mantenimiento y la escalabilidad del sistema.</p>
<hr>
<h2>Conclusión Final del Módulo Late-Pass</h2>
<p>El Módulo Late-Pass es una pieza de ingeniería de software sólida, bien planificada y ejecutada.</p>
<ul>
<li><strong>Fortalezas Clave</strong>:
<ul>
<li><strong>Arquitectura Consistente</strong>: El uso repetido del patrón (Interfaz de Filtros -> JS -> API -> Tabla Dinámica) en todas las secciones de consulta es ejemplar.</li>
<li><strong>Separación de Responsabilidades</strong>: La división entre presentación (HTML), interacción (JS) y lógica (PHP/API) es clara y sigue las mejores prácticas.</li>
<li><strong>Experiencia de Usuario Fluida</strong>: Las interfaces son dinámicas, responden rápidamente a las acciones del usuario y proporcionan feedback visual claro.</li>
<li><strong>Lógica de Negocio Robusta</strong>: Las reglas para el registro de movimientos son específicas y están protegidas por transacciones de base de datos.</li>
</ul>
</li>
<li><strong>Punto Menor de Mejora Sugerido</strong>:
<ul>
<li>Las APIs de consulta para Staff y Vehículos podrían mejorarse para manejar la opción de "Todos", que actualmente se ofrece en la interfaz pero no está implementada en el backend. Habilitar esta funcionalidad proporcionaría una visión general valiosa para los administradores.</li>
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