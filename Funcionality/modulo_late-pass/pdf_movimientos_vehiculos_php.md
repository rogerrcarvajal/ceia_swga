# Documentación del Archivo: `src/reports_generators/pdf_movimientos_vehiculos.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar un **reporte en formato PDF** de los movimientos de entrada y salida de vehículos. Es invocado desde la página de consulta (`pages/gestion_vehiculos.php`) y utiliza los filtros de semana y vehículo para producir un documento formal que detalla el historial de movimientos vehiculares.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetros**: El script obtiene los filtros `semana` y `vehiculo_id` a través de la URL (`GET`).

2.  **Obtención de Datos**: El script ejecuta la **misma consulta SQL** que la API `api/consulta_movimientos_vehiculos.php`. Esta consulta une las tablas `registro_vehiculos`, `vehiculos` y `estudiantes` para obtener todos los detalles de los movimientos, incluyendo la placa, el modelo, el nombre del estudiante asociado, la fecha, las horas de entrada/salida y quién registró el movimiento.

3.  **Generación del PDF (FPDF)**: 
    *   Utiliza la librería `FPDF` para crear el documento PDF. La orientación de la página se establece en `L` (Landscape) para acomodar mejor la tabla de datos.
    *   Establece un título principal dinámico que incluye el nombre del estudiante asociado al vehículo (si se filtró por un vehículo específico).
    *   Define la estructura de la tabla con columnas para Fecha, Placa, Modelo, Hora Entrada, Hora Salida y Registrado Por.
    *   Itera sobre cada registro de movimiento obtenido de la base de datos y dibuja una fila en la tabla por cada uno, manejando los valores nulos (como `hora_salida` si el vehículo aún está dentro) con un guion "-".

4.  **Salida del Documento**: Finalmente, utiliza el método `$pdf->Output('I', ...)` para enviar el PDF generado directamente al navegador. La opción `'I'` (inline) hace que el PDF se muestre en el navegador en lugar de forzar una descarga, y se le asigna un nombre de archivo dinámico.

---

## 3. Observación sobre Código Duplicado

Es importante notar que la lógica de consulta a la base de datos para obtener los registros de movimientos vehiculares es idéntica en este archivo y en `api/consulta_movimientos_vehiculos.php`. Para futuras mejoras y facilitar el mantenimiento, esta lógica podría ser extraída a una función o clase compartida que ambos archivos puedan utilizar.
