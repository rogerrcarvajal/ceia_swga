# Documentación del Archivo: `src/reports_generators/generar_latepass_pdf.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar un **reporte en formato PDF** de las llegadas tarde de los estudiantes. Es invocado desde la página de consulta (`pages/gestion_latepass.php`) y utiliza los filtros de semana y grado para producir un documento formal que detalla las tardanzas, incluyendo el conteo de "strikes" semanales.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetros**: El script obtiene los filtros `semana` (número de semana ISO) y `grado` (opcional) a través de la URL (`GET`).

2.  **Obtención de Datos**: El script ejecuta la **misma consulta SQL compleja** que la API `api/consultar_latepass.php`. Esta consulta une las tablas `llegadas_tarde`, `estudiantes`, `estudiante_periodo` y `latepass_resumen_semanal` para obtener todos los detalles de las llegadas tarde, incluyendo el `grado_cursado`, la `fecha_registro`, `hora_llegada`, el `conteo_tardes` (como `strikes`) y el `ultimo_mensaje`.

3.  **Agrupación de Datos**: Una vez obtenidos los registros, el script los agrupa por `grado_cursado`. Esto es útil para organizar el reporte en secciones por grado, facilitando la lectura.

4.  **Generación del PDF (FPDF)**: 
    *   Define una clase `LatePassPDF` que extiende `FPDF`, personalizando el `Header` (con el logo del colegio y el período activo) y el `Footer` (con información de contacto y paginación).
    *   Añade una página al PDF y establece un título principal dinámico (ej. "Late-Pass de la Semana X").
    *   **Iteración por Grado**: Recorre los datos agrupados. Para cada grado, añade un subtítulo y luego una tabla.
    *   **Tabla de Detalles**: Dentro de cada sección de grado, crea una tabla con columnas para "Estudiante", "Fecha", "Hora", "Strikes" y "Observación".
    *   Rellena la tabla con los datos de cada estudiante, utilizando `utf8_decode` para asegurar la correcta visualización de caracteres especiales.

5.  **Salida del Documento**: Finalmente, utiliza el método `$pdf->Output('D', ...)` para enviar el PDF generado directamente al navegador. La opción `'D'` (Download) fuerza la descarga del archivo con un nombre descriptivo (ej. `LatePass_Semana_29_Grade_5.pdf`).

---

## 3. Observación sobre Código Duplicado

Es importante notar que la lógica de consulta a la base de datos para obtener los registros de llegadas tarde es idéntica en este archivo y en `api/consultar_latepass.php`. Para futuras mejoras y facilitar el mantenimiento, esta lógica podría ser extraída a una función o clase compartida que ambos archivos puedan utilizar.

--teras