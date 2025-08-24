# Documentación del Archivo: `pages/menu_mantto.php`

## 1. Propósito del Archivo

Este archivo PHP actúa como el **menú principal y punto de acceso centralizado** para todas las funcionalidades de mantenimiento y administración del sistema SWGA. Su objetivo es proporcionar a los usuarios con rol `master` una interfaz clara para gestionar aspectos críticos del sistema, como los períodos escolares, los usuarios y los respaldos de la base de datos.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

La lógica del lado del servidor de esta página es mínima y se enfoca en la seguridad y el contexto:

1.  **Control de Acceso Estricto**: Verifica que el usuario esté autenticado y, crucialmente, que su rol sea **`master`**. El acceso a este módulo está altamente restringido para proteger las operaciones críticas del sistema.
2.  **Obtención del Período Activo**: Consulta la base de datos para obtener y mostrar el nombre del período escolar activo, proporcionando un contexto relevante para las operaciones de mantenimiento.

---

## 3. Estructura de la Interfaz y Opciones

La página presenta un menú de opciones claro y directo, utilizando el estilo `lista-menu` para consistencia visual. Ofrece las siguientes tres opciones principales de mantenimiento:

1.  **Establecer Períodos Escolares**
    *   **Enlace**: `pages/periodos_escolares.php`
    *   **Descripción**: "Permite crear y activar un nuevo Periodo escolar, el cual vinculara información de Estudiantes, Madre, Madre y Staff con el período escolar activo."
    *   **Funcionalidad**: Dirige a una página donde se pueden crear, gestionar y activar los diferentes períodos escolares del sistema.

2.  **Gestión de Usuarios del Sistema**
    *   **Enlace**: `pages/configurar_usuarios.php`
    *   **Descripción**: "Permite la creación y gestión de usuarios del sistema, vinculando al staff registrado como un usuario en el periodo escolar activo."
    *   **Funcionalidad**: Conduce a una interfaz para administrar las cuentas de usuario que pueden acceder al sistema, incluyendo la asignación de roles.

3.  **Gestión de Backup**
    *   **Enlace**: `pages/backup_db.php`
    *   **Descripción**: "Permite la creación y restauración de respaldos de la base de datos."
    *   **Funcionalidad**: Dirige a la página donde se pueden realizar respaldos manuales de la base de datos y gestionar los archivos de respaldo existentes.
