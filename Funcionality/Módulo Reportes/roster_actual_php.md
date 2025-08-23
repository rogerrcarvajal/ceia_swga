# Documentación del Archivo: `src/reports_generators/roster_actual.php`

## 1. Propósito del Archivo

Este archivo PHP es un **generador de reportes HTML dinámico** que presenta un "Roster" o directorio completo del personal y los estudiantes para el período escolar activo. Su objetivo es proporcionar una vista consolidada y organizada de la estructura académica y administrativa de la institución, con la posibilidad de exportar esta información a un formato PDF.

---

## 2. Lógica de Negocio y Flujo de Operación

La lógica de este script es intensiva en consultas y procesamiento de datos para estructurar el roster.

1.  **Control de Acceso**: Verifica que el usuario tenga los roles `admin`, `master` o `consulta` para acceder al reporte.
2.  **Verificación de Período Activo**: Consulta la base de datos para obtener el ID y el nombre del período escolar activo. Si no hay un período activo, el reporte no se puede generar y se muestra un mensaje.
3.  **Obtención de Datos del Personal**: 
    *   Realiza una consulta `JOIN` entre `profesor_periodo` y `profesores` para obtener el nombre completo, categoría y posición de todo el personal asignado al período activo, filtrando por `Staff Administrativo` y `Staff Docente`.
    *   **Clasificación de Docentes**: El script implementa una lógica de negocio específica para clasificar al `Staff Docente` en áreas como `Preschool`, `Elementary`, `Secondary` y `Especiales`, basándose en la `posicion` del profesor y un mapa predefinido (`$mapa_areas`).
    *   **Ordenamiento de Administrativos**: El `Staff Administrativo` se ordena según un array predefinido (`$orden_admin`) para asegurar una jerarquía específica en el reporte.
    *   **Maestros de Homeroom**: Consulta la base de datos para identificar a los profesores que son `homeroom_teacher` y los asocia con sus respectivos grados.
4.  **Obtención y Agrupación de Datos de Estudiantes**: 
    *   Realiza una consulta `JOIN` entre `estudiante_periodo` y `estudiantes` para obtener el nombre completo y el grado de todos los estudiantes asignados al período activo.
    *   Los estudiantes se agrupan en un array (`$estudiantes_por_grado`) donde la clave es el grado y el valor es una lista de estudiantes de ese grado. Esto facilita la presentación por secciones.

---

## 3. Estructura de la Interfaz (HTML)

El reporte se presenta en HTML con varias secciones claras:

*   **Cabecera**: Muestra el logo del colegio y el título del reporte, incluyendo el nombre del período activo.
*   **Staff Administrativo**: Una tabla simple que lista al personal administrativo, ordenado por su posición.
*   **Staff Docente**: Secciones separadas para cada área (Preschool, Elementary, etc.), listando a los docentes con su posición.
*   **Listado de Estudiantes por Grado**: 
    *   Para cada grado, se muestra el nombre del `homeroom_teacher` asociado.
    *   Se lista a los estudiantes de ese grado, ordenados alfabéticamente por apellido y nombre.
*   **Botón "Generar PDF"**: Un enlace que apunta a `src/reports_generators/generar_roster_pdf.php`, permitiendo al usuario obtener una versión imprimible del roster.
