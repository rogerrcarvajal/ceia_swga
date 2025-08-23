# Documentación del Archivo: `pages/backup_db.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona la interfaz de usuario para la **gestión de respaldos de la base de datos** del sistema SWGA. Permite a los administradores con rol `master`:

*   Realizar **respaldos manuales** de la base de datos en formato `.sql`.
*   **Visualizar un historial** de los respaldos existentes.
*   **Descargar** cualquier archivo de respaldo previamente generado.

Es una herramienta crítica para la recuperación de desastres y la integridad de los datos del sistema.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Carga (PHP - Método GET)

1.  **Control de Acceso**: Solo los usuarios con rol `master` pueden acceder a esta página. Cualquier otro rol es redirigido al dashboard con un mensaje de acceso denegado.
2.  **Verificación de Período Activo**: Consulta el período escolar activo (proporciona contexto en la interfaz, aunque no es directamente funcional para el backup).
3.  **Listado de Respaldos**: Utiliza la función `glob()` para buscar todos los archivos que coincidan con el patrón `ceia_db_backup_*.sql` dentro del directorio `PostgreSQL-DB/`. Los archivos encontrados se ordenan por fecha (los más recientes primero) y se listan en el panel derecho de la interfaz.

### b. Lógica de Descarga de Archivos (Método GET - `download_file`)

Si la página es invocada con un parámetro `download_file` en la URL, el script actúa como un gestor de descargas seguro:

1.  **Validación de Seguridad**: Extrae el nombre del archivo solicitado y construye la ruta completa. **Crucialmente**, utiliza `preg_match()` para validar que el nombre del archivo coincida con el patrón esperado (`^ceia_db_backup_(\\\d{8})_(\\\d{6})\.sql$`) y `file_exists()` para asegurar que el archivo realmente existe. Esto previene ataques de "Directory Traversal" donde un atacante podría intentar descargar archivos fuera del directorio de respaldos.
2.  **Envío de Cabeceras**: Si el archivo es válido y existe, el script envía las cabeceras HTTP necesarias (`Content-Description`, `Content-Type`, `Content-Disposition`, `Content-Length`, etc.) para forzar la descarga del archivo por parte del navegador.
3.  **Lectura y Salida**: Lee el contenido del archivo de respaldo y lo envía directamente al flujo de salida del navegador (`readfile()`).

### c. Lógica de Respaldo Manual (PHP - Método POST)

Cuando el administrador hace clic en el botón "Realizar Respaldo Ahora":

1.  **Ruta de `pg_dump`**: Obtiene la ruta al ejecutable `pg_dump` de PostgreSQL desde la constante `PG_DUMP_PATH` definida en `src/config.php`.
2.  **Directorio de Respaldo**: Define el directorio donde se guardarán los respaldos (`PostgreSQL-DB/`) y lo crea si no existe.
3.  **Nombre del Archivo**: Genera un nombre de archivo único para el respaldo utilizando la fecha y hora actuales (ej. `ceia_db_backup_YYYYMMDD_HHMMSS.sql`).
4.  **Contraseña de la BD**: Establece temporalmente la contraseña de la base de datos como una variable de entorno (`PGPASSWORD`) utilizando `putenv()`. Esto es necesario para que `pg_dump` pueda autenticarse sin pedir la contraseña interactivamente.
5.  **Construcción del Comando**: Utiliza `sprintf()` para construir el comando completo de `pg_dump`, incluyendo el usuario, host, nombre de la base de datos y la ruta de salida del archivo.
6.  **Ejecución del Comando**: Ejecuta el comando `pg_dump` utilizando `exec()`. Captura la salida estándar y el código de retorno del comando.
7.  **Manejo de Resultados**: 
    *   Si el comando `pg_dump` devuelve un código de retorno diferente de 0 (indicando un error), se construye un mensaje de error detallado que incluye el código de retorno, la salida del comando y el comando ejecutado, lo cual es muy útil para la depuración.
    *   Si el comando es exitoso, se muestra un mensaje de éxito.
8.  **Limpieza**: Elimina la variable de entorno `PGPASSWORD` para no dejar la contraseña expuesta.

---

## 3. Estructura de la Interfaz (HTML)

La página presenta un diseño de dos paneles:

*   **Panel Izquierdo ("Respaldo Manual")**:
    *   Contiene un mensaje informativo sobre la recomendación de respaldos automáticos.
    *   Un botón "Realizar Respaldo Ahora" que, al ser presionado, activa la lógica de respaldo manual.

*   **Panel Derecho ("Historial de Respaldos")**:
    *   Muestra una lista de todos los archivos de respaldo `.sql` encontrados en el directorio `PostgreSQL-DB/`.
    *   Junto a cada archivo, se proporciona un enlace "Descargar" que activa la lógica de descarga segura del script.
