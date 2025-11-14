# Funcionalidad del API `consultar_movimiento_vehiculos.php`

Este endpoint de la API se encarga de consultar y devolver los registros de entrada y salida de los vehículos de los estudiantes. Ofrece la flexibilidad de filtrar los resultados por una semana específica y/o por un vehículo en particular.

## Lógica de Negocio

1.  **Configuración Inicial:**
    *   Establece la zona horaria a `America/Caracas` para estandarizar el manejo de fechas.
    *   Define la cabecera de la respuesta como `application/json` para asegurar que el cliente interprete correctamente los datos devueltos.

2.  **Recepción de Parámetros:**
    *   El script captura dos parámetros opcionales enviados mediante una solicitud GET:
        *   `semana`: Una cadena que identifica una semana (ej. '2024-W30'). El script usa esta fecha para calcular el día de inicio y fin de esa semana.
        *   `vehiculo_id`: El ID numérico de un vehículo para acotar la búsqueda a ese único vehículo.

3.  **Construcción Dinámica de la Consulta:**
    *   La consulta SQL se construye de forma dinámica para adaptarse a los filtros proporcionados.
    *   Si se recibe el parámetro `semana`, se añade una cláusula `WHERE` para que la `fecha` del registro esté comprendida entre el primer y el último día de la semana indicada.
    *   Si se recibe `vehiculo_id`, se añade una cláusula `WHERE` para filtrar por ese ID.
    *   Si ambos parámetros están presentes, las condiciones se combinan con un operador `AND` para una búsqueda más específica.

4.  **Consulta a la Base de Datos:**
    *   La consulta principal une tres tablas:
        1.  `registro_vehiculos`: Contiene los datos de los movimientos (fechas, horas).
        2.  `vehiculos`: Para obtener los detalles del vehículo (placa, modelo).
        3.  `estudiantes`: Para obtener el nombre del estudiante asociado al vehículo.
    *   Se seleccionan los campos más relevantes para el reporte: placa, modelo, nombre del estudiante, fecha, hora de entrada/salida y el usuario que registró el movimiento.
    *   Los resultados se ordenan de forma descendente por fecha y hora de entrada, mostrando siempre los registros más recientes primero.
    *   Se emplean sentencias preparadas de PDO para ejecutar la consulta, lo que previene ataques de inyección SQL.

5.  **Respuesta JSON:**
    *   En caso de éxito, la API devuelve un objeto JSON con un `status: 'ok'` y un array `data` que contiene todos los registros encontrados.
    *   Si se produce cualquier error o excepción, devuelve un objeto JSON con un `status: 'error'` y un `message` que describe el fallo.
