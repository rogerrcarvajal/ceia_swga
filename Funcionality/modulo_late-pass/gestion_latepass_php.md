# Documentación del Archivo: `pages/gestion_latepass.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona la interfaz de usuario para la **consulta y visualización de los registros de llegadas tarde (Late-Pass)** de los estudiantes. Su función es permitir a los administradores auditar y generar reportes sobre las tardanzas de los estudiantes, filtrando los resultados por semana y por grado.

La página en sí misma solo establece la estructura básica y los filtros; toda la lógica de obtención y presentación dinámica de los datos es manejada por el archivo JavaScript `public/js/gestion_latepass.js`.

---

## 2. Lógica de Carga (PHP)

La lógica del lado del servidor en la carga inicial de la página es la siguiente:

1.  **Control de Acceso**: Valida la sesión del usuario y su rol (`admin`, `master` o `consulta`), permitiendo el acceso a personal autorizado.
2.  **Obtención de Grados**: Consulta la base de datos para obtener una lista de todos los `grado_cursado` distintos que tienen estudiantes asignados en el período escolar activo. Esta lista se utiliza para poblar el menú desplegable de filtro por grado.

---

## 3. Estructura de la Interfaz (HTML)

La página está organizada para facilitar la consulta de los registros de Late-Pass:

*   **Contenedor de Filtros (`filtros-container`)**: Una sección en la parte superior que incluye:
    *   Un selector de semana (`<input type="week" id="filtro_semana">`).
    *   Un menú desplegable (`<select id="filtro_grado">`) con los grados disponibles, poblado por PHP.

*   **Tabla de Resultados (`staff-table`)**: Una tabla HTML con una cabecera que define las columnas para los datos de Late-Pass (Estudiante, Grado, Fecha de Llegada, Hora de Llegada, Strikes Semanales, Observaciones). El cuerpo de la tabla (`<tbody id="tabla_resultados_latepass">`) está inicialmente vacío y será llenado dinámicamente por JavaScript.

*   **Botones de Acción**:
    *   **Generar PDF**: Un botón (`<button id="btnGenerarPDF">`) que, al ser presionado, activará la generación de un reporte PDF con los datos filtrados.
    *   **Volver**: Un enlace para regresar al menú principal del módulo de Late-Pass.

---

## 4. Vínculo con el Frontend

La interactividad y la funcionalidad principal de la página son proporcionadas por el script `public/js/gestion_latepass.js`, que se carga al final del archivo. Este script es responsable de:

*   Detectar cambios en los filtros de semana y grado.
*   Realizar llamadas a la API `api/consultar_latepass.php` para obtener los datos de llegadas tarde.
*   Renderizar dinámicamente los resultados en la tabla.
*   Manejar la lógica para la generación del reporte PDF (`src/reports_generators/generar_latepass_pdf.php`).
