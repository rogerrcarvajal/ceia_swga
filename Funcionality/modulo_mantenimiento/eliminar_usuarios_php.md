# Documentación del Archivo: `pages/eliminar_usuarios.php`

## 1. Propósito del Archivo

Este archivo PHP es un **script de backend no interactivo** cuya única función es **eliminar una cuenta de usuario** del sistema. Se invoca desde la página de gestión de usuarios (`pages/configurar_usuarios.php`) y, tras realizar las validaciones de seguridad, procede con la eliminación del registro en la base de datos.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso Estricto**: El script implementa una medida de seguridad muy específica y restrictiva: solo permite la ejecución de la eliminación si el usuario autenticado tiene el `username` exactamente igual a `'superusuario'`. Cualquier otro usuario, incluso con rol `master`, será denegado.
2.  **Verificación de ID**: Obtiene el `id` del usuario a eliminar desde el parámetro `GET` en la URL. Si el ID no es válido o no se proporciona, redirige al usuario a la página de gestión de usuarios.
3.  **Prevención de Auto-Eliminación**: Antes de proceder con la eliminación, el script verifica si el `id` del usuario que se intenta eliminar es el mismo que el `id` del usuario actualmente logueado (`$_SESSION['usuario']['id']`). Si coinciden, la eliminación es abortada y se establece un mensaje de error en la sesión, impidiendo que un administrador se elimine a sí mismo accidentalmente y se bloquee el acceso al sistema.
4.  **Ejecución de la Eliminación**: Si todas las validaciones de seguridad y lógica de negocio pasan, el script ejecuta una consulta `DELETE` preparada sobre la tabla `usuarios`.
5.  **Feedback y Redirección**: 
    *   Si ocurre un error durante la eliminación (ej. `PDOException`), se establece un mensaje de error en la sesión.
    *   Independientemente del resultado (éxito o error), el script siempre redirige al usuario de vuelta a la página `pages/configurar_usuarios.php`, donde se mostrará el mensaje de estado correspondiente.

---

## 3. Observaciones de Seguridad

*   **Restricción por Username**: La restricción de acceso por `username` (`superusuario`) es una medida de seguridad muy fuerte, pero también inflexible. Si este usuario se pierde o se olvida la contraseña, la gestión de usuarios podría quedar comprometida.
*   **Mensajes de Sesión**: El uso de `$_SESSION` para los mensajes de feedback es una buena práctica, ya que permite mostrar el resultado de la operación después de la redirección.
