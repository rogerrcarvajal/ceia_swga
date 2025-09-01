# Documentación del Archivo: `src/reports_generators/generar_lista_staff_mantenimiento_PDF.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar un **reporte en formato PDF con la lista del personal de mantenimiento** asignado al período escolar activo. Proporciona un listado formal con detalles como el nombre completo, la posición y el teléfono.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: Verifica que el usuario esté autenticado y tenga los roles `master`, `admin` o `consulta`.
2.  **Inclusión de Librerías**: Carga `config.php` y `fpdf.php`.
3.  **Obtención del Período Activo**: Consulta la base de datos para obtener el ID y el nombre del período escolar activo.
4.  **Obtención de Datos de Staff**: Ejecuta una consulta SQL que une `profesor_periodo` y `profesores`. La consulta filtra específicamente por `p.categoria = 'Staff Mantenimiento'`.
    *   **Nota**: A diferencia de los reportes de staff administrativo y docente, este reporte no incluye el conteo de hijos staff, ya que no es relevante para esta categoría.
5.  **Generación del PDF (FPDF)**: 
    *   Define una clase `PDF` que extiende `FPDF`, personalizando el `Header` (logo, título del reporte, período activo) y el `Footer` (paginación).
    *   Añade una página al PDF.
    *   Establece el título "Reporte de Staff Mantenimiento".
    *   Define los encabezados de la tabla: Nombre Completo, Posición, Teléfono.
    *   Itera sobre los datos del personal de mantenimiento y rellena las filas de la tabla.
6.  **Salida del Documento**: Envía el PDF generado directamente al navegador con el método `$pdf->Output('D', ...)` forzando la descarga del archivo.

---

## 3. Observación sobre Código Duplicado

La lógica de obtención de datos de staff es idéntica a la que se encuentra en `pages/gestionar_reportes.php` y en otros generadores de reportes de staff. Para futuras mejoras y facilitar el mantenimiento, esta lógica podría ser extraída a una función o clase compartida.
