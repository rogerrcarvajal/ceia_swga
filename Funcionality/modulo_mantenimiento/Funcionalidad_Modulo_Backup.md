
# Documentación del Módulo de Respaldo de Base de Datos

## 1. Propósito del Módulo

El propósito de este módulo es proporcionar una interfaz gráfica dentro del sistema SWGA para que un usuario administrador (con rol 'master') pueda crear un respaldo completo de la base de datos PostgreSQL (`ceia_db`) en cualquier momento. Esto es una funcionalidad crítica para la recuperación de datos en caso de fallos del sistema, corrupción de datos o cualquier otro desastre.

## 2. Lógica de Negocio y Flujo de Operación

El proceso se gestiona a través del archivo `pages/backup_db.php` y sigue los siguientes pasos:

### a. Control de Acceso y Seguridad

1.  **Autenticación de Usuario**: El script primero verifica si hay una sesión de usuario activa. Si el usuario no está autenticado, es redirigido inmediatamente a la página de inicio de sesión (`index.php`).
2.  **Autorización por Rol**: Una vez autenticado, el sistema verifica el rol del usuario. El acceso a esta funcionalidad está restringido exclusivamente a los usuarios con el rol de **'master'**. Si un usuario con un rol diferente intenta acceder, se le muestra un mensaje de "Acceso denegado" y es redirigido al panel principal (`dashboard.php`). Esto asegura que solo el personal autorizado pueda realizar operaciones tan sensibles como el respaldo de la base de datos.

### b. Proceso de Respaldo (Al presionar el botón)

Cuando el usuario 'master' hace clic en el botón "Realizar Respaldo Ahora", se envía una solicitud `POST` que desencadena la siguiente lógica:

1.  **Definición de Rutas**:
    *   Se obtiene la ruta completa al ejecutable `pg_dump.exe` desde una constante `PG_DUMP_PATH` definida en el archivo de configuración `src/config.php`. Mantener esta ruta en un archivo de configuración facilita el despliegue del sistema en diferentes entornos sin tener que modificar el código fuente.
    *   Se define el directorio donde se guardará el respaldo: `PostgreSQL-DB/`.
    *   Se genera un nombre de archivo único para el respaldo, combinando un prefijo (`ceia_db_backup_`), la fecha y hora actual (`Ymd_His`), y la extensión `.sql`. Ejemplo: `ceia_db_backup_20250822_103000.sql`. Esto permite mantener un historial de respaldos ordenado cronológicamente.

2.  **Manejo Seguro de Credenciales**:
    *   Para que el comando `pg_dump` pueda conectarse a la base de datos sin requerir una contraseña de forma interactiva (lo cual no es posible desde un script PHP), se utiliza la función `putenv()`.
    *   Se establece una variable de entorno temporal `PGPASSWORD` con la contraseña de la base de datos, que también se obtiene del archivo `config.php`. Esta es la forma recomendada por PostgreSQL para manejar contraseñas en scripts y procesos automatizados.

3.  **Construcción del Comando de Respaldo**:
    *   Se utiliza la función `sprintf` para construir de forma segura y legible el comando `pg_dump` que se ejecutará en el servidor.
    *   El comando final tiene la siguiente estructura:
        ```bash
        "C:\Program Files\PostgreSQL\17\bin\pg_dump.exe" -U postgres -h localhost -d ceia_db -F p -E UTF-8 -f "C:\xampp\htdocs\ceia_swga\PostgreSQL-DB\ceia_db_backup_20250822_103000.sql" 2>&1
        ```
    *   **Componentes del Comando**:
        *   `"ruta\a\pg_dump.exe"`: El comando a ejecutar (entre comillas para manejar espacios en la ruta).
        *   `-U postgres`: Especifica el **Usuario** de la base de datos.
        *   `-h localhost`: El **Host** o servidor de la base de datos.
        *   `-d ceia_db`: El **Nombre de la Base de Datos** a respaldar.
        *   `-F p`: Especifica el **Formato** de salida como "plain" (un script SQL de texto plano).
        *   `-E UTF-8`: Establece la **Codificación** de caracteres a UTF-8.
        *   `-f "ruta\al\archivo.sql"`: Especifica el **Archivo de Salida** para el respaldo (entre comillas para seguridad).
        *   `2>&1`: Redirige la salida de errores (stderr) a la salida estándar (stdout), lo que permite capturar cualquier mensaje (de éxito o de error) en una sola variable.

4.  **Ejecución y Verificación**:
    *   Se utiliza la función `exec()` de PHP para ejecutar el comando en el sistema operativo. `exec()` es preferible a `shell_exec()` en este caso porque permite obtener el **código de retorno** del comando.
    *   Un código de retorno `0` significa que el comando se ejecutó con éxito. Cualquier otro valor indica un error.
    *   Si el código de retorno no es `0`, el script captura los mensajes de error generados por `pg_dump` y los muestra en la interfaz, informando al usuario que hubo un problema y proporcionando los detalles técnicos del fallo.
    *   Si el código de retorno es `0`, se muestra un mensaje de éxito en la pantalla, junto con el nombre del archivo de respaldo generado.

5.  **Limpieza de Seguridad**:
    *   Inmediatamente después de ejecutar el comando, se vuelve a llamar a `putenv('PGPASSWORD=')` para limpiar la variable de entorno que contenía la contraseña. Esto es una medida de seguridad importante para asegurar que la contraseña no persista en el entorno del servidor más tiempo del estrictamente necesario.

## 3. Configuración Requerida

Para que este módulo funcione correctamente, es indispensable que el archivo `src/config.php` contenga las siguientes constantes definidas con los valores correctos para el entorno del servidor:

*   `DB_HOST`: El host de la base de datos (ej. 'localhost').
*   `DB_NAME`: El nombre de la base de datos (ej. 'ceia_db').
*   `DB_USER`: El usuario de la base de datos (ej. 'postgres').
*   `DB_PASSWORD`: La contraseña del usuario.
*   `PG_DUMP_PATH`: La ruta absoluta al archivo `pg_dump.exe` en la instalación de PostgreSQL del servidor.

