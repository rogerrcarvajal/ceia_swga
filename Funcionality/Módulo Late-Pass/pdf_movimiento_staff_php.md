# Documentación del Archivo: `src/reports_generators/pdf_movimiento_staff.php`

## 1. Propósito del Archivo

Este es un script de backend cuya única finalidad es generar un reporte en formato **PDF** de los registros de asistencia del personal. Es invocado por el botón "Generar PDF" de la página de consulta de asistencia (`gestion_es_staff.php`) y utiliza los mismos filtros (semana y/o personal) para crear un documento formal y listo para imprimir o archivar.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Inclusión de Librerías**: El script carga la configuración de la base de datos (`config.php`) y la librería para la creación de PDFs (`fpdf.php`).

2.  **Recepción de Parámetros**: Obtiene los filtros `semana` y `staff_id` (opcional) de la URL (`GET`).

3.  **Obtención de Datos**: El script replica la misma lógica de consulta que la API `api/consultar_movimiento_staff.php`. Construye una consulta SQL dinámica con un `JOIN` y una cláusula `WHERE` basada en los filtros para obtener los registros de asistencia de la tabla `entrada_salida_staff`.

4.  **Construcción del PDF (FPDF)**: Una vez que tiene los datos, comienza a construir el documento PDF paso a paso:
    *   Crea una nueva página A4.
    *   Establece las fuentes y un título dinámico que incluye el nombre del personal si se filtró por una persona específica.
    *   Dibuja la cabecera de la tabla (Fecha, Hora Entrada, Hora Salida, Ausente).
    *   Itera sobre cada registro de asistencia obtenido de la base de datos y dibuja una fila en la tabla por cada uno.
    *   Maneja los valores nulos (como horas no registradas) mostrándolos como un guion "-".

5.  **Salida del Documento**: Finalmente, utiliza el método `$pdf->Output('I', ...)` para enviar el PDF generado directamente al navegador. La opción `'I'` (inline) hace que el PDF se muestre en el navegador en lugar de forzar una descarga, y se le asigna un nombre de archivo dinámico.

---

## 3. Observación sobre Código Duplicado

La lógica para consultar la base de datos (el bloque de código que construye y ejecuta la consulta SQL) es idéntica a la que se encuentra en `api/consultar_movimiento_staff.php`. En una futura refactorización, esta lógica podría extraerse a una función o clase compartida para evitar la duplicación de código y facilitar el mantenimiento.
