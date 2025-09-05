# Funcionalidad de `validaciones.js`

Este archivo contiene funciones de validación reutilizables para los formularios del lado del cliente en toda la aplicación. Su objetivo es verificar los datos ingresados por el usuario antes de que sean enviados al servidor.

## Lógica de Negocio

### Función `validarEstudiante()`

*   **Propósito:** Se utiliza para validar los campos del formulario de registro o edición de un estudiante.
*   **Funcionamiento:**
    1.  Selecciona el campo de entrada correspondiente al nombre del estudiante (`input[name="nombre"]`).
    2.  Obtiene el valor del campo y elimina espacios en blanco al inicio y al final con `trim()`.
    3.  Comprueba si la longitud del nombre es inferior a 3 caracteres.
    4.  Si la validación no se cumple, muestra un `alert()` de JavaScript con un mensaje de error para el usuario y retorna `false`. Este `false` puede ser utilizado para detener el envío del formulario.
    5.  Si el nombre tiene 3 o más caracteres, la función retorna `true`, indicando que la validación fue exitosa.

> **Nota:** Este archivo está diseñado para ser escalable, permitiendo añadir más funciones de validación para otros formularios (ej. `validarProfesor()`, `validarVehiculo()`, etc.) a medida que el sistema crece.
