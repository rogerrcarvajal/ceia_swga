# Funcionalidad y Lógica del Módulo de Autorización de Salida de Estudiantes

Este documento detalla la arquitectura y el flujo de trabajo del módulo diseñado para registrar y gestionar las autorizaciones de salida de los estudiantes durante el horario escolar.

## Componentes Principales

El módulo se compone de los siguientes archivos:

1.  **`pages/planilla_salida.php`**: Formulario principal para la creación de una nueva autorización de salida.
2.  **`pages/gestion_planilla_salida.php`**: Interfaz para la visualización y consulta de las autorizaciones ya registradas.
3.  **`public/js/consultar_salidas.js`**: Script del lado del cliente que gestiona la interactividad en la página de gestión, como la carga dinámica de registros.
4.  **`api/guardar_autorizacion_salida.php`**: Endpoint de la API para procesar y guardar en la base de datos los datos de una nueva autorización.
5.  **`api/consultar_salidas.php`**: Endpoint de la API para buscar y devolver los registros de autorizaciones según los filtros aplicados.
6.  **`src/reports_generators/generar_autorizacion_pdf.php`**: Script que genera el comprobante de la autorización en formato PDF.

## Flujo de Trabajo

### 1. Creación de una Autorización

-   **Acceso**: El usuario accede a `pages/planilla_salida.php`.
-   **Selección de Estudiante**: El formulario permite buscar y seleccionar un estudiante activo en el período escolar actual.
-   **Autocompletado de Datos**: Al seleccionar un estudiante, el sistema realiza una llamada a la API (`api/obtener_padre.php`, `api/obtener_madre.php`) para obtener los datos del padre y la madre, permitiendo al usuario seleccionarlos como la persona que retira.
-   **Ingreso de Datos**: El usuario completa la información restante: fecha, hora, motivo y quién retira (si es una persona distinta a los padres).
-   **Guardado**: Al hacer clic en "Guardar Autorización", los datos del formulario se envían mediante una petición POST a `api/guardar_autorizacion_salida.php`.
-   **Persistencia**: El script de la API valida los datos y los inserta en la tabla `autorizaciones_salida` de la base de datos. Se asocia el registro con el ID del estudiante y el ID del usuario que realiza la operación.

### 2. Gestión y Consulta de Autorizaciones

-   **Acceso**: El usuario navega a `pages/gestion_planilla_salida.php`.
-   **Carga Inicial**: Al cargar la página, el script `public/js/consultar_salidas.js` se ejecuta automáticamente. Por defecto, establece el filtro de la semana a la semana actual.
-   **Petición a la API**: El JavaScript realiza una petición `fetch` a `api/consultar_salidas.php`, enviando la semana seleccionada y el filtro de estudiante (por defecto, "Todos"). Para evitar problemas de caché, se añade un parámetro único (timestamp) a la URL en cada petición.
-   **Consulta en BD**: La API `consultar_salidas.php` recibe los parámetros, calcula las fechas de inicio y fin de la semana, y ejecuta una consulta SQL sobre la tabla `autorizaciones_salida` para obtener los registros que coincidan con los filtros.
-   **Respuesta**: La API devuelve los resultados en formato JSON.
-   **Renderizado en Tabla**: El script `consultar_salidas.js` recibe la respuesta JSON, limpia la tabla de resultados y la vuelve a poblar dinámicamente con los registros obtenidos.
-   **Filtrado Interactivo**: Si el usuario cambia la semana o selecciona un estudiante específico, se repite el proceso de petición a la API y repintado de la tabla, mostrando los nuevos resultados.

### 3. Generación de PDF

-   **Acción**: Desde la planilla de salida (o la gestión), el usuario puede hacer clic en un botón para generar el PDF.
-   **Llamada al Script**: Esta acción redirige a `src/reports_generators/generar_autorizacion_pdf.php`, pasando el `id` de la autorización como parámetro en la URL.
-   **Generación del Documento**: El script obtiene los datos completos de la autorización desde la base de datos, instancia una clase FPDF personalizada (con encabezado y pie de página) y renderiza los datos en un documento PDF con formato de tamaño Carta.
-   **Salida**: El PDF generado se muestra en el navegador para su visualización o impresión.
