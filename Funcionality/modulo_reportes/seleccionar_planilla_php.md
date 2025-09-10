# Documentación del Archivo: `pages/seleccionar_planilla.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona una interfaz simple para que los administradores puedan **generar la planilla de inscripción completa de un estudiante específico** en formato PDF. Es el punto de entrada para obtener una versión imprimible y detallada del expediente de un estudiante, tal como fue registrado inicialmente.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

1.  **Control de Acceso**: Verifica que el usuario esté autenticado y tenga los roles `master` o `admin`.
2.  **Obtención del Período Activo**: Consulta la base de datos para obtener el ID del período escolar que está actualmente activo. Esto es crucial porque la lista de estudiantes que se mostrará se filtra por este período.
3.  **Carga de Estudiantes**: Realiza una consulta `JOIN` entre las tablas `estudiante_periodo` y `estudiantes` para obtener el `id`, `nombre_completo` y `apellido_completo` de **todos los estudiantes que están asignados al período escolar activo**. La lista se ordena alfabéticamente por apellido.

---

## 3. Estructura de la Interfaz (HTML)

La página presenta un formulario muy sencillo:

*   **Menú Desplegable (`<select name="id">`)**: Este es el elemento principal de la interfaz. Se rellena dinámicamente con la lista de estudiantes obtenida por la lógica PHP. Cada opción (`<option>`) tiene el ID del estudiante como `value` y su nombre completo como texto visible.
*   **Botón "Generar PDF"**: Al hacer clic en este botón, el formulario se envía.

*   **Atributos del Formulario**: El formulario tiene los siguientes atributos clave:
    *   `action="/ceia_swga/src/reports_generators/generar_planilla_pdf.php"`: Indica que los datos del formulario (el ID del estudiante seleccionado) serán enviados a este script PHP, que es el encargado de generar el PDF.
    *   `method="GET"`: Los datos se envían como parámetros en la URL.
    *   `target="_blank"`: Asegura que el PDF generado se abra en una nueva pestaña del navegador, permitiendo al usuario mantener la página de selección abierta.
