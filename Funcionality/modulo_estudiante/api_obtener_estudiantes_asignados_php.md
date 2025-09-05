# Funcionalidad del API `obtener_estudiantes_asignados.php`

Este script se utiliza para obtener un listado de todos los estudiantes que ya han sido asignados a un período escolar específico.

## Lógica de Negocio

1.  **Recepción de Parámetros:** El script espera recibir un `periodo_id` a través de una solicitud GET. Este ID es crucial para identificar el período escolar del cual se desean obtener los estudiantes.

2.  **Validación:** Si no se proporciona un `periodo_id`, el script termina su ejecución y devuelve un array JSON vacío, indicando que no se puede proceder sin este dato.

3.  **Consulta a la Base de Datos:**
    *   Se ejecuta una consulta SQL que une las tablas `estudiante_periodo` y `estudiantes`.
    *   El objetivo es seleccionar la información relevante de los estudiantes (`id`, `nombre_completo`, `apellido_completo`) y el grado que están cursando en ese período (`grado_cursado`).
    *   La consulta se filtra usando el `periodo_id` proporcionado para asegurar que solo se listen los estudiantes de ese período.
    *   Los resultados se ordenan alfabéticamente por el apellido y luego por el nombre del estudiante para facilitar la visualización.

4.  **Respuesta:**
    *   Si la consulta es exitosa, el script devuelve un array JSON que contiene los datos de todos los estudiantes asignados.
    *   En caso de un error en la conexión o en la consulta a la base de datos, el script responde con un código de estado HTTP 500 y un mensaje de error en formato JSON.
