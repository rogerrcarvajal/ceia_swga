# Documentación del Archivo: `pages/gestion_es_staff.php`

## 1. Propósito del Archivo

Este archivo PHP constituye la interfaz de usuario para la **consulta y visualización de los registros de asistencia** (entradas y salidas) del personal. A diferencia de una página de registro en tiempo real, esta herramienta está diseñada para que los administradores puedan auditar y generar reportes de la asistencia del staff, filtrando los resultados por semana y por persona.

La página en sí solo monta la estructura y los filtros; toda la lógica de consulta y presentación de datos es manejada por el archivo JavaScript `public/js/gestion_es_staff.js`.

---

## 2. Lógica de Carga (PHP)

La lógica del lado del servidor en la carga inicial es sencilla y se enfoca en preparar los filtros:

1.  **Control de Acceso**: Valida la sesión del usuario y su rol (`master` o `admin`).
2.  **Obtención de Personal**: Realiza una consulta a la base de datos para obtener la lista de todo el personal (`profesores`) que está asignado al período escolar activo. 
3.  **Poblado de Filtros**: Utiliza la lista de personal obtenida para rellenar dinámicamente el menú desplegable (`<select id="filtro_staff">`), permitiendo al administrador seleccionar un miembro del staff específico para la consulta.

---

## 3. Estructura de la Interfaz (HTML)

La página está estructurada para facilitar la consulta de datos:

*   **Contenedor de Filtros (`filtros-container`)**: Una sección en la parte superior que contiene los dos controles principales para la consulta:
    *   Un selector de semana (`<input type="week">`).
    *   Un menú desplegable con la lista del personal.

*   **Tabla de Resultados (`staff-table`)**: Una tabla HTML estándar con una cabecera (`<thead>`) que define las columnas (Nombre, Fecha, Hora Entrada, Hora Salida, Ausente). El cuerpo de la tabla (`<tbody id="tabla_resultados_staff">`) está inicialmente vacío, mostrando un mensaje para que el usuario seleccione filtros. Este `tbody` será poblado dinámicamente por JavaScript.

*   **Botones de Acción**:
    *   **Generar PDF**: Un botón que, presumiblemente, utiliza los filtros seleccionados para generar un reporte en PDF de la asistencia.
    *   **Volver**: Un enlace para regresar a la página anterior.

---

## 4. Vínculo con el Frontend

La funcionalidad interactiva de la página depende completamente del script `public/js/gestion_es_staff.js`, que se carga al final del archivo. Este script es responsable de:

*   Detectar cambios en los filtros de semana y personal.
*   Realizar una llamada a una API para buscar los registros de asistencia que coincidan con los filtros.
*   Limpiar y repoblar la tabla de resultados con los datos recibidos.
*   Manejar la lógica del botón "Generar PDF".
