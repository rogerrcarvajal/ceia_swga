# Documentación del Archivo: `pages/eliminar_vehiculo.php`

## 1. Propósito del Archivo

Este es un **script puramente lógico** que no renderiza ninguna interfaz HTML. Su única responsabilidad es procesar la solicitud de eliminación de un vehículo y redirigir al usuario de vuelta a la lista de vehículos.

Se activa cuando el administrador hace clic en el enlace "Eliminar" de la página `registro_vehiculos.php` y confirma la acción en el diálogo del navegador.

---

## 2. Lógica de Negocio y Flujo de Operación

El flujo de ejecución es directo y no requiere interacción del usuario más allá de la confirmación inicial:

1.  **Control de Acceso y Sesión**: Verifica que el usuario esté autenticado y tenga los permisos necesarios (`master` o `admin`).
2.  **Recepción y Validación de ID**: Obtiene el `id` del vehículo desde el parámetro en la URL (`$_GET['id']`). Si el ID no es válido o no se proporciona, interrumpe la ejecución y redirige inmediatamente a la página de registro de vehículos con un mensaje de error.
3.  **Ejecución de la Eliminación**: 
    *   Envuelve la operación en un bloque `try...catch` para manejar posibles errores de la base de datos.
    *   Prepara y ejecuta una consulta `DELETE FROM vehiculos WHERE id = :id`.
4.  **Verificación y Feedback**: 
    *   Utiliza `$stmt->rowCount()` para comprobar si la consulta afectó a alguna fila. `rowCount() > 0` significa que el vehículo fue encontrado y eliminado con éxito.
    *   Basado en el resultado, almacena un mensaje apropiado (éxito, no encontrado, o error) en la variable de sesión `$_SESSION['mensaje_vehiculo']`.
5.  **Redirección**: Sin importar el resultado, el script siempre finaliza con una redirección (`header("Location: ...")`) de vuelta a la página `registro_vehiculos.php`, donde se mostrará el mensaje de estado guardado en la sesión.
