# Documentación del Archivo: `pages/configurar_usuarios.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona la interfaz de usuario para la **gestión centralizada de las cuentas de usuario** del sistema SWGA. Permite a los administradores con rol `master`:

*   **Crear** nuevas cuentas de usuario.
*   **Asignar roles** a los usuarios (Master, Administrador, Consulta).
*   **Vincular** cuentas de usuario a miembros del staff existentes.
*   **Visualizar** una lista de todos los usuarios registrados, con información sobre su rol y si están vinculados a un miembro del staff.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Carga (PHP - Método GET)

1.  **Control de Acceso Estricto**: Solo los usuarios con rol `master` pueden acceder a esta página. Cualquier otro rol es redirigido al dashboard con un mensaje de acceso denegado.
2.  **Gestión de Mensajes**: Recupera y limpia cualquier mensaje de éxito o error almacenado en la sesión (`$_SESSION['mensaje_usuario']`, `$_SESSION['error_mensaje']`) que provenga de operaciones anteriores (ej. después de editar o eliminar un usuario).
3.  **Verificación de Período Activo**: Consulta el período escolar activo (aunque no es directamente funcional para la gestión de usuarios, proporciona contexto en la interfaz).
4.  **Obtención de Usuarios Existentes**: Recupera todos los usuarios de la tabla `usuarios`, realizando un `LEFT JOIN` con la tabla `profesores` para mostrar el nombre del miembro del staff al que está vinculado el usuario (si lo está).
5.  **Obtención de Staff sin Usuario**: Consulta la tabla `profesores` para obtener una lista de todos los miembros del staff que **aún no tienen una cuenta de usuario vinculada**. Esta lista se utiliza para poblar el menú desplegable de vinculación en el formulario de creación de usuario.

### b. Lógica de Creación de Usuario (PHP - Método POST)

Cuando un administrador envía el formulario de "Agregar Usuario":

1.  **Recepción de Datos**: Captura `profesor_id` (opcional), `username`, `password` y `rol` desde la variable `$_POST`.
2.  **Cifrado de Contraseña**: **Crucialmente**, la contraseña (`$clave`) proporcionada por el usuario es cifrada utilizando `password_hash($clave, PASSWORD_DEFAULT)` antes de ser almacenada en la base de datos. Esto asegura que las contraseñas nunca se guarden en texto plano, protegiéndolas de accesos no autorizados.
3.  **Verificación de Nombre de Usuario Duplicado**: Antes de la inserción, el script verifica si ya existe un usuario con el `username` proporcionado. Si es así, impide la creación y muestra un mensaje de error.
4.  **Inserción en Base de Datos**: Si el `username` es único, ejecuta una consulta `INSERT` preparada para añadir el nuevo registro a la tabla `usuarios`.
    *   Si `profesor_id` es una cadena vacía, se guarda como `NULL` en la base de datos, indicando que el usuario no está vinculado a un miembro del staff específico.
5.  **Feedback al Usuario**: Se establece una variable `$mensaje` para informar al administrador sobre el resultado de la operación.

---

## 3. Estructura de la Interfaz (HTML)

La página presenta un diseño de dos paneles:

*   **Panel Izquierdo ("Gestión de Usuario")**:
    *   Contiene un formulario para crear nuevos usuarios.
    *   Incluye un menú desplegable para vincular el usuario a un miembro del staff (solo muestra el staff que aún no tiene un usuario).
    *   Campos para `username`, `password` (con una opción para mostrar/ocultar la contraseña) y un menú desplegable para seleccionar el `rol` (Master, Administrador, Consulta).

*   **Panel Derecho ("Usuarios Registrados")**:
    *   Muestra una lista de todos los usuarios existentes en el sistema.
    *   Para cada usuario, se muestra su `username`, `rol` y el `nombre_completo` del miembro del staff al que está vinculado (o "No vinculado").
    *   **Acciones Condicionales**: Para cada usuario listado, se proporcionan enlaces "Editar" y "Eliminar". Sin embargo, el usuario actualmente logueado no puede editarse ni eliminarse a sí mismo, lo que previene el bloqueo accidental del sistema.
