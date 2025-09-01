# Documentación del Archivo: `src/reports_generators/generar_lista_estudiantes_PDF.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar un **reporte en formato PDF con la lista completa de estudiantes** matriculados en el período escolar activo. El reporte incluye información clave de cada estudiante, como su grado y los datos de contacto de sus padres, siendo una herramienta útil para directorios o listas de emergencia.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: Verifica que el usuario esté autenticado y tenga los roles `master`, `admin` o `consulta`.
2.  **Inclusión de Librerías**: Carga `config.php` (para la conexión a la BD) y `fpdf.php` (para la generación de PDF).
3.  **Obtención del Período Activo**: Consulta la base de datos para obtener el ID y el nombre del período escolar activo, que es el contexto para el reporte.
4.  **Obtención de Datos de Estudiantes**: Ejecuta una consulta SQL compleja que une las tablas `estudiantes`, `estudiante_periodo`, `padres` y `madres`. Esta consulta es idéntica a la utilizada en `pages/gestionar_reportes.php` para la previsualización de estudiantes. Obtiene:
    *   Nombre y apellido completo del estudiante.
    *   Grado cursado en el período activo.
    *   Nombre, apellido, celular y email del padre y la madre.
5.  **Generación del PDF (FPDF)**: 
    *   Define una clase `PDF` que extiende `FPDF`, personalizando el `Header` (logo, título del reporte, período activo) y el `Footer` (paginación).
    *   Añade una página al PDF (orientación horizontal `L` para acomodar más columnas).
    *   Establece los encabezados de la tabla: Nombre Completo, Grado, Padre / Madre, Teléfonos, Email.
    *   Itera sobre los datos de los estudiantes y rellena las filas de la tabla, utilizando `utf8_decode` para caracteres especiales y manejando valores `N/A` para datos faltantes.
6.  **Salida del Documento**: Envía el PDF generado directamente al navegador con el método `$pdf->Output('D', ...)` lo que fuerza la descarga del archivo con un nombre descriptivo (ej. `lista_estudiantes_Periodo_2024-2025.pdf`).

---

## 3. Observación sobre Código Duplicado

La lógica de obtención de datos de estudiantes es idéntica a la que se encuentra en `pages/gestionar_reportes.php`. Para futuras mejoras y facilitar el mantenimiento, esta lógica podría ser extraída a una función o clase compartida que ambos archivos puedan utilizar.
