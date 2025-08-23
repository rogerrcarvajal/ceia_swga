# Documentación del Archivo: `src/reports_generators/generar_lista_vehiculos_autorizados_pdf.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar un **reporte en formato PDF con la lista completa de vehículos autorizados** en el plantel. El reporte incluye la placa, el modelo y el nombre del estudiante asociado a cada vehículo, siendo una herramienta útil para el control de acceso y la administración vehicular.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: Verifica que el usuario esté autenticado y tenga los roles `master`, `admin` o `consulta`.
2.  **Inclusión de Librerías**: Carga `config.php` (para la conexión a la BD) y `fpdf.php` (para la generación de PDF).
3.  **Obtención del Período Activo**: Consulta la base de datos para obtener el nombre del período escolar activo, que se muestra en el encabezado del reporte.
4.  **Obtención de Datos de Vehículos**: Ejecuta una consulta SQL que une las tablas `vehiculos` y `estudiantes` para obtener la placa, el modelo, el estado de autorización y el nombre completo del estudiante asociado a cada vehículo. Esta consulta es idéntica a la utilizada en `pages/gestionar_reportes.php` para la previsualización de vehículos.
5.  **Generación del PDF (FPDF)**: 
    *   Define una clase `PDF` que extiende `FPDF`, personalizando el `Header` (logo, título del reporte, período activo) y el `Footer` (paginación).
    *   Añade una página al PDF (orientación vertical `P`).
    *   Establece los encabezados de la tabla: Estudiante, Placa, Modelo, Autorizado.
    *   Itera sobre los datos de los vehículos y rellena las filas de la tabla, utilizando `utf8_decode` para caracteres especiales y mostrando el estado de autorización como "Sí" o "No".
6.  **Salida del Documento**: Envía el PDF generado directamente al navegador con el método `$pdf->Output('D', ...)` lo que fuerza la descarga del archivo con un nombre descriptivo (ej. `lista_vehiculos_autorizados_Periodo_2024-2025.pdf`).

---

## 3. Observación sobre Código Duplicado

La lógica de obtención de datos de vehículos es idéntica a la que se encuentra en `pages/gestionar_reportes.php`. Para futuras mejoras y facilitar el mantenimiento, esta lógica podría ser extraída a una función o clase compartida que ambos archivos puedan utilizar.
