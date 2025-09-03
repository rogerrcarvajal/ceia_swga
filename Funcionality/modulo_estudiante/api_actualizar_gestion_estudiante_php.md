# Funcionalidad del API `actualizar_gestion_estudiante.php`

Este script se encarga de actualizar la información de la asignación de un estudiante a un período escolar específico.

## Lógica de Negocio

1.  **Recepción de Datos:** El script espera una solicitud POST con los siguientes datos:
    *   `id`: El identificador único de la asignación del estudiante en la tabla `estudiante_periodo`.
    *   `field`: El campo que se desea modificar.
    *   `value`: El nuevo valor para el campo especificado.

2.  **Validación de Campos:** Para garantizar la seguridad y la integridad de los datos, se utiliza una lista blanca (`$allowed_fields`) que define qué campos de la tabla `estudiante_periodo` pueden ser modificados a través de esta API. Actualmente, el único campo permitido es:
    *   `grado_cursado`

3.  **Procesamiento de Datos:**
    *   Si el campo `grado_cursado` recibe el valor 'N/A' o una cadena vacía, el script lo interpreta como un valor `NULL` para ser almacenado en la base de datos. Esta lógica permite "limpiar" o dejar sin definir el grado de un estudiante.

4.  **Actualización en la Base de Datos:**
    *   Si el `id` es válido y el `field` está dentro de la lista de campos permitidos, se prepara y ejecuta una consulta SQL `UPDATE` para modificar el registro correspondiente en la tabla `estudiante_periodo`.
    *   Se utilizan sentencias preparadas de PDO para prevenir inyecciones SQL.

5.  **Respuesta:**
    *   El script devuelve una respuesta en formato JSON.
    *   Si la actualización es exitosa, la respuesta contiene un estado `success` y un mensaje de confirmación.
    *   Si ocurren errores (por ejemplo, datos incompletos, un campo no permitido o un fallo en la base de datos), la respuesta contendrá un estado `error` y un mensaje descriptivo del problema.
