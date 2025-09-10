# Documentación del Módulo de Staff

## 1. Propósito del Módulo

El Módulo de Staff está diseñado para la gestión completa del personal de la institución, que incluye docentes, personal administrativo y de mantenimiento. El término "Profesor" se usa a menudo en el código como sinónimo de "miembro del Staff".

Este módulo permite registrar nuevos miembros del personal, gestionar su información, asignarlos a períodos escolares y controlar sus movimientos de entrada y salida del plantel, generando también los códigos QR para su identificación.

## 2. Flujo de Trabajo y Componentes

El módulo se articula a través de varias páginas, APIs y scripts que cubren todo el ciclo de vida de un miembro del personal en el sistema.

### a. Registro de Nuevo Personal

*   **Página**: `pages/profesores_registro.php`
*   **Propósito**: Proporciona un formulario para dar de alta a un nuevo miembro del personal en el sistema.
*   **Lógica de Negocio**:
    1.  El administrador completa los datos personales del miembro del staff (cédula, nombres, apellidos, teléfono, cargo, etc.).
    2.  Al enviar el formulario, los datos son validados y enviados a una API (probablemente `api/asignar_profesores.php` o similar) que inserta un nuevo registro en la tabla `staff` de la base de datos.
    3.  El sistema genera automáticamente un código QR único asociado a la cédula del miembro del personal.

### b. Gestión de Personal Existente

*   **Página Principal**: `pages/gestionar_profesor.php`
*   **Archivos Involucrados**:
    *   `public/js/admin_profesores.js` (Lógica del Frontend para la gestión).
    *   `api/obtener_profesores.php` (API para listar todos los profesores).
    *   `api/actualizar_profesores.php` (API para guardar cambios).
    *   `pages/eliminar_profesor.php` (Script para eliminar un registro).
*   **Flujo de Operación**:
    1.  **Visualización y Búsqueda**: La página muestra una lista de todo el personal registrado. Un campo de búsqueda permite filtrar los resultados para una localización rápida.
    2.  **Selección y Edición**: El administrador puede seleccionar un miembro del personal para ver sus detalles y activar el modo de edición.
    3.  **Actualización (AJAX)**: El script `admin_profesores.js` maneja la lógica de la interfaz. Al guardar los cambios, se realiza una llamada asíncrona a `api/actualizar_profesores.php` para actualizar la información en la base de datos sin necesidad de recargar la página.
    4.  **Eliminación**: Se proporciona una opción para eliminar a un miembro del personal, que probablemente redirige al script `eliminar_profesor.php` para procesar la baja.

### c. Control de Acceso y Movimientos (E/S)

Esta es una funcionalidad clave para la seguridad y el registro de asistencia del personal.

*   **Página**: `pages/gestion_es_staff.php`
*   **Archivos Involucrados**:
    *   `public/js/gestion_es_staff.js` (Lógica del Frontend).
    *   `api/registrar_movimiento_staff.php` (API para guardar la entrada/salida).
    *   `api/consultar_movimiento_staff.php` (API para verificar el último estado de un miembro).
*   **Flujo de Operación**:
    1.  **Interfaz de Registro**: La página presenta una interfaz simple, probablemente con una cámara para escanear códigos QR o un campo para introducir la cédula.
    2.  **Escaneo/Entrada de Cédula**: El administrador escanea el QR del miembro del staff o introduce su cédula.
    3.  **Verificación de Estado (AJAX)**: `gestion_es_staff.js` envía la cédula a `api/consultar_movimiento_staff.php`. Esta API revisa la tabla `movimientos_staff` para determinar si el último movimiento registrado fue una entrada o una salida.
    4.  **Registro de Movimiento (AJAX)**: Basado en el estado actual, el sistema propone la acción contraria (si está "Adentro", propone "Registrar Salida" y viceversa). Al confirmar, `gestion_es_staff.js` llama a `api/registrar_movimiento_staff.php` para insertar el nuevo evento (entrada o salida) en la base de datos con la fecha y hora actual.
    5.  **Feedback Visual**: La interfaz se actualiza en tiempo real para mostrar el resultado de la operación y el estado actual del miembro del personal.

### d. Generación de Códigos QR

*   **Página/Reporte**: `src/reports_generators/generar_qr_staff_pdf.php`
*   **Propósito**: Generar un documento PDF que contiene el código QR de un miembro del staff, listo para ser impreso y utilizado como identificación.
*   **Lógica**: Este script toma la cédula de un miembro del personal, utiliza la librería `phpqrcode` para generar la imagen del código QR y luego usa la librería `FPDF` para incrustar esa imagen en un archivo PDF con un formato predefinido.

## 3. Reportes Asociados al Módulo

El módulo de Staff está vinculado a varios reportes importantes que se generan desde el **Módulo de Reportes**.

*   `generar_lista_staff_admin_PDF.php`: Genera un listado en PDF del personal administrativo.
*   `generar_lista_staff_docente_PDF.php`: Genera un listado en PDF del personal docente.
*   `generar_lista_staff_mantenimiento_PDF.php`: Genera un listado en PDF del personal de mantenimiento.
*   `pdf_movimiento_staff.php`: Genera un reporte detallado de los movimientos (entradas y salidas) del personal en un rango de fechas específico.

Estos scripts consultan las tablas `staff` y `movimientos_staff` y utilizan la librería `FPDF` para formatear y presentar los datos en documentos PDF.
