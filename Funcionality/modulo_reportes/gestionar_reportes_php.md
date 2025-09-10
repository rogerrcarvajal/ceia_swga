# Documentación del Archivo: `pages/gestionar_reportes.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona una **interfaz centralizada y dinámica para la generación de diversos reportes detallados** en formato PDF. Actúa como un panel de control donde los administradores pueden seleccionar una categoría de reporte (Estudiantes, Staff por tipo, Vehículos), previsualizar los datos relevantes en una tabla y luego generar el documento PDF completo.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Carga (PHP)

La parte PHP de este archivo es intensiva en la obtención de datos, ya que debe precargar toda la información necesaria para las previsualizaciones de los reportes:

1.  **Control de Acceso**: Verifica que el usuario tenga los roles `admin`, `master` o `consulta`.
2.  **Período Activo**: Obtiene el ID y nombre del período escolar activo, que es el contexto para todos los reportes.
3.  **Datos de Estudiantes**: Realiza una consulta compleja que une `estudiantes`, `estudiante_periodo`, `padres` y `madres` para obtener una lista completa de estudiantes con sus grados y la información de contacto de sus padres. Esta información se usa para la previsualización del reporte de estudiantes.
4.  **Datos de Staff (Categorizado)**: 
    *   Realiza consultas para obtener el personal asignado al período activo, filtrado por categorías: `Staff Administrativo`, `Staff Docente` y `Staff Mantenimiento`.
    *   Incluye una subconsulta para determinar el número de hijos que son estudiantes del mismo staff, lo que añade un dato relevante al reporte de staff.
5.  **Datos de Vehículos**: Consulta `vehiculos` y `estudiantes` para obtener la lista de vehículos autorizados junto con el nombre del estudiante asociado.

Todos estos datos se `json_encode`n y se incrustan directamente en el HTML para ser utilizados por el JavaScript del frontend.

### b. Lógica del Frontend (JavaScript Embebido)

El script JavaScript embebido es el encargado de la interactividad de la interfaz:

1.  **Manejo del Menú Lateral**: Escucha los eventos `click` en los elementos de la lista del menú lateral (`.menu-lateral li`).
2.  **Visualización Dinámica**: Cuando se selecciona una categoría de reporte:
    *   Se gestiona la clase `active` para resaltar el ítem del menú seleccionado.
    *   Se oculta el panel informativo inicial y todas las demás secciones de previsualización.
    *   Se muestra únicamente la sección de previsualización (`.preview-section`) correspondiente a la categoría seleccionada (`data-target`).
3.  **Generación de PDF**: Cada sección de previsualización contiene un formulario con un botón "Generar PDF". El atributo `action` de estos formularios apunta directamente al script PHP generador de PDF específico para ese reporte (ej. `generar_lista_estudiantes_PDF.php`). El atributo `target="_blank"` asegura que el PDF se abra en una nueva pestaña.

---

## 3. Estructura de la Interfaz (HTML)

La página está organizada en un diseño de dos paneles:

*   **Menú Lateral (`menu-lateral`)**: Contiene una lista de categorías de reportes (Estudiantes, Staff Administrativo, Staff Docente, Staff Mantenimiento, Vehículos Autorizados).
*   **Panel de Selección (`panel-seleccion`)**: Este panel muestra dinámicamente la previsualización del reporte seleccionado. Cada previsualización es un `div` (`.preview-section`) que contiene:
    *   Un título del reporte.
    *   Una tabla (`.preview-table`) con una muestra de los datos, poblada directamente por PHP.
    *   Un formulario con un botón para generar el PDF completo del reporte.
