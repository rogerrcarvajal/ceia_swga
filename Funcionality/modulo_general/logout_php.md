# Funcionalidad de `logout.php`

Este script gestiona el proceso de cierre de sesión de un usuario en el sistema.

## Lógica de Negocio

El proceso es directo y sigue los pasos estándar para un cierre de sesión seguro:

1.  **`session_start()`:** Inicia o reanuda la sesión actual. Es un paso necesario para poder manipular la información de la sesión que se va a destruir.

2.  **`session_unset()`:** Libera (elimina) todas las variables de sesión que se habían registrado, como `$_SESSION['usuario']` y `$_SESSION['rol']`. Esto asegura que no queden datos residuales del usuario en la sesión.

3.  **`session_destroy()`:** Destruye toda la información asociada con la sesión actual en el servidor. Este es el paso final para eliminar la sesión por completo.

4.  **`header("Location: ...")`:** Redirige al usuario a la página de inicio de sesión pública (`index.php`). Esto proporciona una transición clara de salida del sistema.

5.  **`exit()`:** Termina la ejecución del script inmediatamente después de la redirección para asegurar que no se procese ningún código adicional.
