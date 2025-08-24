
# Documentación del Módulo de Mantenimiento

## 1. Propósito del Módulo

El módulo de Mantenimiento centraliza las tareas administrativas críticas para la salud y seguridad del sistema SWGA. Actualmente, su principal funcionalidad es la gestión de respaldos de la base de datos, pero está diseñado para poder incorporar futuras herramientas de administración del sistema.

El acceso a este módulo está estrictamente restringido al rol de usuario **'master'**.

## 2. Funcionalidad: Respaldo de Base de Datos (`backup_db.php`)

Esta sección permite la creación de respaldos manuales y la visualización y descarga de todos los respaldos existentes.

### a. Lógica de Negocio y Flujo de Operación

1.  **Respaldo Manual**: Un usuario 'master' puede generar un respaldo instantáneo de la base de datos `ceia_db` presionando el botón "Realizar Respaldo Ahora". El proceso técnico es el mismo que se describió en el documento anterior (`Funcionalidad_Modulo_Backup.md`): se invoca al programa `pg_dump` del servidor de forma segura para generar un archivo `.sql` con la fecha y hora actual en su nombre.

2.  **Historial de Respaldos**: Esta nueva sección muestra una lista de todos los archivos de respaldo (`.sql`) que se encuentran en el directorio `PostgreSQL-DB/`. La lista se presenta en orden cronológico descendente (los más recientes primero), permitiendo al administrador ver el historial completo de respaldos, tanto los generados manualmente como los automáticos.

3.  **Descarga de Respaldos**: Junto a cada archivo en el historial, hay un botón "Descargar". Al hacer clic, el navegador inicia la descarga del archivo `.sql` seleccionado. Esto se logra a través de un gestor de descargas seguro implementado en el mismo archivo `backup_db.php`, que verifica que el archivo solicitado sea un respaldo válido antes de enviarlo al usuario.

### b. Configuración de Respaldos Automáticos (Tarea Programada en Windows)

Como se mencionó, la automatización de respaldos no se gestiona desde la página web, sino desde el sistema operativo del servidor. A continuación se detalla cómo configurar un respaldo diario automático en Windows utilizando el **Programador de Tareas**.

**Objetivo**: Ejecutar el comando de respaldo de la base de datos todos los días a una hora específica (por ejemplo, a las 11:00 PM).

**Paso 1: Crear un script de respaldo**

Es más robusto y seguro ejecutar la tarea desde un archivo de script en lugar de un comando directo. Crea un archivo llamado `backup_script.bat` en una ubicación segura en tu servidor (ej. `C:\Scripts\`).

El contenido del archivo `backup_script.bat` debe ser el siguiente. Este script hace dos cosas: establece la contraseña de la base de datos como una variable de entorno local (solo para este script) y luego ejecuta el comando `pg_dump`.

```batch
@echo off
REM Script para realizar el respaldo diario de la base de datos ceia_db

REM --- CONFIGURACIÓN ---
REM Ruta al ejecutable pg_dump.exe
set PG_DUMP_PATH="C:\Program Files\PostgreSQL\17\bin\pg_dump.exe"

REM Credenciales de la base de datos (ajustar si es necesario)
set PG_HOST=localhost
set PG_USER=postgres
set PGPASSWORD=4674
set DB_NAME=ceia_db

REM Directorio de destino para los respaldos
set BACKUP_DIR="c:\xampp\htdocs\ceia_swga\PostgreSQL-DB"

REM Nombre del archivo de respaldo con fecha
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /format:list') do set datetime=%%I
set BACKUP_FILENAME=%BACKUP_DIR%\ceia_db_backup_%datetime:~0,8%_%datetime:~8,6%.sql

REM --- EJECUCIÓN DEL RESPALDO ---
echo Realizando respaldo de %DB_NAME%...
%PG_DUMP_PATH% -U %PG_USER% -h %PG_HOST% -d %DB_NAME% -F p -E UTF-8 -f %BACKUP_FILENAME%

echo Respaldo completado: %BACKUP_FILENAME%
```

**Paso 2: Configurar la Tarea Programada en Windows**

1.  Abre el **Programador de Tareas** (puedes buscarlo en el menú de inicio de Windows).
2.  En el panel de la derecha, haz clic en **"Crear tarea básica..."**.
3.  **Nombre**: Escribe un nombre descriptivo, como "Respaldo Diario BD CEIA".
4.  **Desencadenador**: Selecciona **"Diariamente"** y establece la hora a la que quieres que se ejecute (ej. 11:00:00 PM).
5.  **Acción**: Selecciona **"Iniciar un programa"**.
6.  **Programa/script**: Haz clic en **"Examinar..."** y busca y selecciona el archivo `backup_script.bat` que creaste en el Paso 1.
7.  **Finalizar**: Revisa la configuración y haz clic en **"Finalizar"**.

Con estos pasos, Windows ejecutará automáticamente el script de respaldo todos los días a la hora especificada, asegurando que siempre tengas una copia de seguridad reciente de tu base de datos. Estos respaldos automáticos aparecerán en el "Historial de Respaldos" de la aplicación web.

## 3. Futuras Mejoras

El módulo de mantenimiento podría expandirse para incluir:

*   **Restauración de Respaldos**: Una interfaz para restaurar la base de datos a partir de un archivo de respaldo existente.
*   **Logs del Sistema**: Un visor para los logs de errores de PHP o de la aplicación.
*   **Optimización de la Base de Datos**: Herramientas para ejecutar comandos de mantenimiento de PostgreSQL como `VACUUM` o `REINDEX`.
