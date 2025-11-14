# Funcionalidad y Lógica: Módulo de Reimpresión de Autorizaciones

## 1. Introducción

Este módulo fue implementado para permitir a los usuarios con los permisos adecuados (Administradores, Master, Consulta) acceder al historial de autorizaciones de salida y reimprimir cualquier planilla generada previamente, tanto para estudiantes como para miembros del personal (staff).

La funcionalidad central es proveer una interfaz de búsqueda y un listado histórico que facilita la recuperación y regeneración de los documentos PDF correspondientes a estas autorizaciones.

## 2. Flujo de Usuario

1.  **Acceso al Módulo:** El usuario navega a `Menú de Reportes` y selecciona la nueva opción **"Autorizaciones de Estudiantes/Staff Generadas"**.
2.  **Página de Selección:** Se le presenta una página (`regenerar_autorizaciones.php`) con un menú lateral que contiene dos categorías: `Estudiantes` y `Staff`.
3.  **Selección de Categoría:** Al hacer clic en una categoría, una petición asíncrona (fetch) carga una lista desplegable con todas las personas correspondientes (todos los estudiantes o todo el personal, sin filtros de período).
4.  **Búsqueda de Historial:** El usuario selecciona una persona de la lista y presiona el botón **"Ver Autorizaciones"**.
5.  **Visualización de Historial:** El sistema redirige a una página de historial (`autorizaciones_estudiantes_generadas.php` o `autorizaciones_staff_generadas.php`) que muestra una tabla con todas las autorizaciones de salida registradas para esa persona.
6.  **Reimpresión de PDF:** Cada fila de la tabla tiene un botón **"Generar PDF"**. Al hacer clic, se invoca al generador de PDF correspondiente (`generar_autorizacion_pdf.php` o `generar_permiso_staff_pdf.php`) con el ID de la autorización específica, forzando la descarga del archivo.

## 3. Componentes del Sistema

La implementación se compone de los siguientes archivos nuevos y modificados:

### Frontend (Páginas PHP)

-   `pages/menu_reportes.php` (Modificado):
    -   Se añadió el enlace al nuevo módulo.
-   `pages/regenerar_autorizaciones.php` (Nuevo):
    -   Página principal que contiene la interfaz de selección de categorías (Estudiantes/Staff).
-   `pages/autorizaciones_estudiantes_generadas.php` (Nuevo):
    -   Muestra la tabla con el historial de autorizaciones para un estudiante específico.
-   `pages/autorizaciones_staff_generadas.php` (Nuevo):
    -   Muestra la tabla con el historial de autorizaciones para un miembro del staff específico.

### Frontend (JavaScript)

-   `public/js/regenerar_autorizaciones.js` (Nuevo):
    -   Controla la lógica de la página de selección. Carga dinámicamente las listas de personas vía API y gestiona la redirección a la página de historial.
-   `public/js/autorizaciones_estudiantes.js` (Nuevo):
    -   Controla la página de historial del estudiante. Obtiene el ID del estudiante de la URL, llama a la API para buscar sus autorizaciones y las muestra en la tabla. Asigna el evento para generar el PDF.
-   `public/js/autorizaciones_staff.js` (Nuevo):
    -   Análogo al anterior, pero para la página de historial del personal.

### Backend (API)

-   `api/obtener_todos_estudiantes.php` (Nuevo):
    -   Endpoint que devuelve una lista completa de todos los estudiantes registrados, sin filtros.
-   `api/obtener_todo_el_staff.php` (Nuevo):
    -   Endpoint que devuelve una lista completa de todo el personal (`profesores`), sin filtros.
-   `api/obtener_autorizaciones_por_estudiante.php` (Nuevo):
    -   Recibe un `estudiante_id` y devuelve todas las entradas correspondientes de la tabla `autorizaciones_salida`.
-   `api/obtener_autorizaciones_por_staff.php` (Nuevo):
    -   Recibe un `staff_id` y devuelve todas las entradas correspondientes de la tabla `autorizaciones_salida_staff`.

### Generadores de Reportes (PDF)

-   `src/reports_generators/generar_autorizacion_pdf.php` (Modificado):
    -   Se cambió el nombre de salida del archivo para que sea descriptivo (`Autorizacion_Nombre_Apellido.pdf`).
    -   Se cambió el método de salida a `D` (descarga forzada) para evitar problemas de caché del navegador.
-   `src/reports_generators/generar_permiso_staff_pdf.php` (Modificado):
    -   Análogo al anterior, pero para los permisos del personal (`Permiso_Nombre_Completo.pdf`).

## 4. Lógica de Negocio y Decisiones de Diseño

-   **Separación de APIs:** Se crearon nuevos endpoints de API (`obtener_todos_estudiantes` y `obtener_todo_el_staff`) en lugar de modificar los existentes (`obtener_estudiantes_por_periodo` y `obtener_profesores`). Esta decisión fue crucial para no afectar otras partes del sistema que dependen del filtrado por período escolar activo.
-   **Carga Asíncrona de Datos:** La página de selección utiliza JavaScript y `fetch` para cargar las listas de personas. Esto es más eficiente que cargar todos los datos con PHP al inicio, especialmente si las listas crecen en el futuro.
-   **Descarga Forzada de PDF:** Se cambió el método de salida de los PDF de `I` (inline) a `D` (download). Esto resuelve un problema común de caché en los navegadores, asegurando que el usuario siempre reciba la versión más reciente del documento y que el nombre de archivo sugerido sea el correcto.
