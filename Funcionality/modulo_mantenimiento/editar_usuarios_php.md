# Documentación del Archivo: `pages/editar_usuarios.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona la interfaz para que un usuario con rol `master` pueda **editar los detalles de una cuenta de usuario existente** en el sistema. Permite modificar el nombre de usuario, el rol y, opcionalmente, la contraseña de la cuenta seleccionada.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Carga (PHP - Método GET)

1.  **Control de Acceso**: Solo los usuarios con rol `master` pueden acceder a esta página. Cualquier otro rol es redirigido al dashboard con un mensaje de acceso denegado.
2.  **Validación de ID**: El script espera un parámetro `id` en la URL, que corresponde al ID del usuario a editar. Si el ID no es válido o no se proporciona, el script redirige a la página principal de configuración de usuarios (`configurar_usuarios.php`) y detiene la ejecución.
3.  **Obtención de Datos del Usuario**: Consulta la base de datos para obtener todos los datos del usuario (`username`, `rol`, etc.) basándose en el `id` proporcionado. Estos datos se utilizan para pre-llenar el formulario de edición.

### b. Lógica de Actualización (PHP - Método POST)

Cuando el administrador envía el formulario de edición:

1.  **Recepción de Datos**: Captura el nuevo `username`, `rol` y `nueva_clave` (la nueva contraseña, si se proporcionó) desde la variable `$_POST`.
2.  **Actualización Condicional de Contraseña**: 
    *   Si el campo `nueva_clave` **no está vacío**, el script entiende que se desea cambiar la contraseña. En este caso, la nueva contraseña es **cifrada** utilizando `password_hash($nueva_clave, PASSWORD_DEFAULT)` antes de ser guardada. La consulta `UPDATE` incluye el campo `password`.
    *   Si el campo `nueva_clave` **está vacío**, la contraseña existente del usuario no se modifica. La consulta `UPDATE` solo incluye los campos `username` y `rol`.
3.  **Manejo de Errores**: El proceso de actualización está envuelto en un bloque `try...catch`. Si ocurre un `PDOException` (por ejemplo, si el nuevo nombre de usuario ya existe), se captura el error y se muestra un mensaje informativo al usuario.
4.  **Feedback al Usuario**: Se establece una variable `$mensaje` para informar al administrador sobre el resultado de la operación.

---

## 3. Estructura de la Interfaz (HTML)

La página presenta un formulario simple pre-llenado con la información actual del usuario:

*   **Campo de Usuario**: Un campo de texto para el `username`.
*   **Menú de Rol**: Un menú desplegable para seleccionar el `rol` del usuario, con las opciones `master`, `admin` y `consulta`.
*   **Campo de Nueva Contraseña**: Un campo de tipo `password` (`nueva_clave`) que permite al administrador introducir una nueva contraseña. Si se deja en blanco, la contraseña actual no se modifica.
*   **Botones**: Incluye un botón "Actualizar Usuario" para guardar los cambios y un botón "Volver" para regresar a la página de gestión de usuarios.
