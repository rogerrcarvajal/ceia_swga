
# Documentación del Módulo de Reportes

## 1. Propósito del Módulo

El Módulo de Reportes es una herramienta de inteligencia de negocio que centraliza la generación de documentos y listados en formato PDF. Su función es tomar los datos almacenados en el sistema y presentarlos de una manera organizada, legible y lista para ser impresa, archivada o distribuida.

Este módulo es esencialmente una interfaz que invoca a diferentes scripts especializados en la creación de reportes específicos, utilizando las librerías FPDF y phpqrcode como base tecnológica. El punto de entrada principal es el **Menú de Reportes** (`pages/menu_reportes.php`) que dirige a la página de gestión.

## 2. Flujo de Trabajo y Componentes

*   **Página Principal**: `pages/gestionar_reportes.php`
*   **Directorio de Generadores**: `src/reports_generators/`

El flujo general es el siguiente:
1.  El administrador navega a la página `gestionar_reportes.php`.
2.  La página presenta un formulario o una serie de botones, cada uno correspondiente a un reporte específico.
3.  Para reportes que requieren parámetros (como un rango de fechas), el administrador los introduce.
4.  Al hacer clic en un botón de "Generar", el formulario se envía o se redirige al script PHP correspondiente dentro de la carpeta `src/reports_generators/`.
5.  El script PHP ejecuta las consultas necesarias a la base de datos, procesa los datos y utiliza la librería FPDF para construir el documento PDF.
6.  El script finaliza enviando el PDF generado directamente al navegador del usuario para su visualización o descarga.

## 3. Listado de Reportes y su Funcionalidad

A continuación se detalla cada uno de los reportes disponibles en el sistema:

### Reportes de Estudiantes
*   **`generar_lista_estudiantes_PDF.php`**: Genera un listado completo de todos los estudiantes inscritos en el período escolar activo, mostrando detalles clave como cédula, nombres, apellidos y grado.
*   **`generar_planilla_pdf.php`**: Genera la planilla de inscripción completa de un estudiante específico, incluyendo sus datos, los de sus padres y su ficha médica. Es una réplica digital del formulario de inscripción.
*   **`generar_qr_pdf.php`**: Crea un documento PDF con el código QR de un estudiante. Este QR usualmente contiene la cédula del estudiante y se usa para el sistema de control de acceso.
*   **`generar_roster_pdf.php`**: Genera el "roster" o listado de un grado y sección específicos, mostrando los estudiantes que pertenecen a esa clase. Es útil para los docentes.

### Reportes de Staff (Personal)
*   **`generar_lista_staff_admin_PDF.php`**: Genera un listado del personal con el cargo "Administrativo".
*   **`generar_lista_staff_docente_PDF.php`**: Genera un listado del personal con el cargo "Docente".
*   **`generar_lista_staff_mantenimiento_PDF.php`**: Genera un listado del personal con el cargo "Mantenimiento".
*   **`generar_qr_staff_pdf.php`**: Crea el carnet o ficha con el código QR de un miembro del personal para el control de acceso.
*   **`pdf_movimiento_staff.php`**: Reporte de auditoría que muestra el historial de entradas y salidas de los miembros del personal, usualmente filtrado por un rango de fechas.

### Reportes de Vehículos
*   **`generar_lista_vehiculos_autorizados_pdf.php`**: Genera un listado de todos los vehículos registrados en el sistema, indicando a qué estudiantes están autorizados a transportar.
*   **`generar_qr_vehiculo_pdf.php`**: Genera la identificación con el código QR para un vehículo, que puede ser pegada en el parabrisas para un rápido escaneo en la garita de seguridad.
*   **`pdf_movimientos_vehiculos.php`**: Reporte de auditoría que muestra el historial de entradas y salidas de los vehículos autorizados.

### Reportes de Gestión y Disciplina
*   **`generar_latepass_pdf.php`**: Como se describió en su propio módulo, genera el comprobante impreso para una llegada tarde específica.

## 4. Lógica de Negocio Clave

*   **Centralización**: El módulo agrupa la lógica de presentación de datos en un solo lugar, facilitando el mantenimiento y la creación de nuevos reportes.
*   **Consistencia Visual**: Al usar FPDF y plantillas comunes, todos los reportes mantienen una apariencia profesional y consistente con la imagen de la institución (logos, colores, etc.).
*   **Seguridad**: Aunque no es visible directamente, es probable que cada script de generación de reportes verifique la sesión del usuario para asegurar que solo personal autorizado pueda generar y acceder a la información sensible.
