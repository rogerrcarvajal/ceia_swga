# Funcionalidad del script `clave.php`

Este script es una herramienta de utilidad para desarrolladores, no forma parte de las funcionalidades principales de la aplicación de cara al usuario.

## Lógica de Negocio

1.  **Propósito:** Su única función es generar un hash seguro de una contraseña para ser almacenado en la base de datos.

2.  **Funcionamiento:**
    *   El script tiene una contraseña predefinida en el código (hardcoded), en este caso, la cadena `'admin'`.
    *   Utiliza la función `password_hash()` de PHP, que es el método estándar y más seguro para crear hashes de contraseñas.
    *   `PASSWORD_DEFAULT` es el algoritmo recomendado, ya que PHP lo mantiene actualizado al más seguro disponible (actualmente bcrypt).
    *   El resultado (el hash generado) se imprime directamente en la pantalla.

## Uso

Un desarrollador ejecutaría este script para obtener el valor hash de una contraseña y luego insertarlo manualmente en la columna `password` de la tabla `usuarios` en la base de datos, por ejemplo, al crear el primer usuario administrador o al necesitar restablecer una contraseña de forma manual.
