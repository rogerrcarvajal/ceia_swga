
# Documentación del Módulo de Late-Pass (Pase de Llegada Tarde)

## 1. Propósito del Módulo

El Módulo de Late-Pass tiene como finalidad gestionar y registrar de manera formal las llegadas tardes de los estudiantes. Esta funcionalidad es crucial para mantener un registro disciplinario y de asistencia preciso, permitiendo a la administración del colegio llevar un control automatizado de los retardos y emitir un justificativo impreso para que el estudiante pueda ingresar a su aula de clases.

El módulo está centralizado en el **Menú de Late-Pass** (`pages/menu_latepass.php`).

## 2. Flujo de Trabajo y Componentes

La operativa del módulo es un proceso lineal y bien definido que involucra la identificación del estudiante, el registro del retardo y la generación de un comprobante en PDF.

### a. Gestión y Generación de Late-Pass

*   **Página Principal**: `pages/gestion_latepass.php`
*   **Archivos Involucrados**:
    *   `public/js/gestion_latepass.js` (Lógica del Frontend).
    *   `api/consultar_latepass.php` (API para buscar al estudiante y registrar el pase).
    *   `src/reports_generators/generar_latepass_pdf.php` (Script para crear el PDF).

*   **Flujo de Operación**:
    1.  **Búsqueda del Estudiante**: La página `gestion_latepass.php` presenta una interfaz donde el administrador puede buscar a un estudiante por su número de cédula.
    2.  **Entrada de Cédula y Consulta (AJAX)**: El administrador introduce la cédula del estudiante y presiona un botón de búsqueda. El script `gestion_latepass.js` captura este evento y realiza una llamada asíncrona (fetch) a la API `api/consultar_latepass.php`.
    3.  **Validación en el Backend**: La API `consultar_latepass.php` recibe la cédula y realiza una consulta a la base de datos (probablemente en la tabla `estudiantes`) para encontrar al estudiante y verificar que esté inscrito y activo en el período escolar actual.
    4.  **Presentación de Datos**: Si el estudiante es encontrado, la API devuelve sus datos (nombre, apellido, grado, etc.) en formato JSON. El script `gestion_latepass.js` recibe esta respuesta y la utiliza para rellenar los campos correspondientes en el formulario, mostrando la información del estudiante en pantalla.
    5.  **Registro del Late-Pass**: El administrador puede añadir una observación (opcional) y luego hace clic en el botón "Generar Pase". Esto no solo prepara la generación del PDF, sino que también realiza una inserción en la tabla `latepass` de la base de datos, registrando el ID del estudiante, la fecha y la hora del retardo.
    6.  **Generación del PDF**: Una vez registrado el retardo, el sistema invoca al script `src/reports_generators/generar_latepass_pdf.php`. Este script toma los datos del estudiante y del retardo recién creado.
    7.  **Creación del Comprobante**: Utilizando la librería FPDF, el script `generar_latepass_pdf.php` crea un documento PDF con un formato oficial que incluye el logo del colegio, los datos del estudiante, la fecha, la hora y un espacio para la firma o sello de la administración. Este PDF se muestra al administrador, listo para ser impreso.

## 3. Lógica de Negocio Clave

*   **Registro Histórico**: Cada vez que se genera un pase, queda un registro permanente en la tabla `latepass`. Esto es fundamental para el **Módulo de Reportes**, que puede explotar esta información para generar estadísticas de retardos por estudiante, por grado o por período.
*   **Justificativo Físico**: El PDF generado no es solo una notificación, sino un documento físico que formaliza el permiso de entrada al aula. Esto cierra el ciclo del proceso administrativo para una llegada tarde.
*   **Integración con Estudiantes**: El módulo está directamente ligado al Módulo de Estudiantes. No se puede generar un pase para alguien que no exista como estudiante activo en el sistema, asegurando la integridad de los datos.
