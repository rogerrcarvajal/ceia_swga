# Análisis de Funcionalidad: Módulo de Mantenimiento

Este documento describe el flujo de trabajo y los componentes técnicos del "Módulo de Mantenimiento", una sección crítica para la configuración global y la seguridad de los datos del sistema.

---

### Arquitectura General

El módulo sigue una arquitectura clásica de PHP, donde las acciones se procesan en el backend a través del envío de formularios y recargas de página. El acceso a todas sus funcionalidades está correctamente restringido al rol de `master`, asegurando que solo los administradores con los más altos privilegios puedan realizar cambios en la configuración y los datos del sistema.

---

### 1. Gestión de Períodos Escolares (`periodos_escolares.php`)

-   **Funcionalidad:** Permite al usuario `master` crear, activar y desactivar los períodos escolares que rigen el funcionamiento de todo el sistema.
-   **Lógica de Negocio Clave:**
    -   Solo se puede crear un nuevo período si no hay otro activo, previniendo inconsistencias.
    -   La activación de un período se realiza dentro de una **transacción de base de datos** para garantizar que solo un período pueda estar activo a la vez.
-   **Componentes Técnicos:** Es una página PHP auto-contenida que procesa sus propios formularios (`POST`).

---

### 2. Gestión de Usuarios del Sistema (`configurar_usuarios.php` y asociados)

-   **Funcionalidad:** Permite al usuario `master` gestionar las cuentas de usuario (`master`, `admin`, `consulta`).
-   **Lógica de Negocio Clave:**
    -   Permite vincular cuentas a miembros del personal existentes.
    -   Las contraseñas se encriptan de forma segura con `password_hash()`.
    -   Un usuario no puede eliminarse a sí mismo.
-   **Componentes Técnicos:** Utiliza un flujo de trabajo de múltiples páginas (`configurar`, `editar`, `eliminar`) para separar las responsabilidades.

---

### 3. Gestión de Backups (`backup_db.php`)

-   **Funcionalidad:** Proporciona una interfaz para crear respaldos manuales de la base de datos y para descargar respaldos existentes.
-   **Flujo de Trabajo y Lógica de Negocio:**
    1.  **Creación:** Un botón "Realizar Respaldo Ahora" ejecuta un script PHP que invoca la utilidad de línea de comandos `pg_dump` del sistema. Esto crea un volcado completo de la base de datos en un archivo `.sql` con fecha y hora en el nombre, guardándolo en la carpeta `/PostgreSQL-DB/`.
    2.  **Listado y Descarga:** La página escanea el directorio de respaldos y muestra una lista de los archivos existentes. Cada archivo tiene un enlace de descarga que, de forma segura, fuerza la descarga del archivo de respaldo solicitado en el navegador del usuario.
-   **Componentes Técnicos:**
    -   Utiliza `exec()` de PHP para interactuar con la utilidad `pg_dump` de PostgreSQL.
    -   Manipula cabeceras HTTP para gestionar las descargas de archivos de forma segura.

---

### Conclusión General del Módulo

El "Módulo de Mantenimiento" es una sección crítica, bien protegida y con una lógica de negocio sólida.

-   **Fortalezas:**
    -   **Seguridad:** El acceso está correctamente restringido por rol y las contraseñas y descargas se manejan de forma segura.
    -   **Integridad de Datos:** Las reglas de negocio, como las transacciones en la activación de períodos y el uso de `pg_dump` para respaldos consistentes, son puntos muy fuertes.
    -   **Flujo de Trabajo Claro:** La separación de las funciones en diferentes scripts hace que la lógica sea fácil de seguir y mantener.