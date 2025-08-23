# Documentación del Archivo: `pages/periodos_escolares.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona la interfaz de usuario para la **gestión completa de los períodos escolares** del sistema. Permite a los administradores con rol `master`:

*   **Crear** nuevos períodos escolares.
*   **Activar** un período escolar, lo que automáticamente desactiva cualquier otro período que estuviera activo.
*   **Desactivar** el período escolar actualmente activo.

Es una herramienta fundamental para la configuración inicial y el mantenimiento continuo del contexto temporal del sistema.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Carga (PHP - Método GET)

1.  **Control de Acceso**: Solo los usuarios con rol `master` pueden acceder a esta página. Si el rol no es `master`, se redirige al dashboard con un mensaje de acceso denegado.
2.  **Verificación de Período Activo**: Al cargar la página, se consulta la base de datos para determinar si ya existe un período escolar marcado como `activo`. Esta información es crucial para la lógica de creación de nuevos períodos.
3.  **Obtención de Todos los Períodos**: Se recuperan todos los períodos escolares registrados en la tabla `periodos_escolares` y se ordenan por fecha de inicio descendente. Esta lista se utiliza para poblar el panel derecho de la interfaz.

### b. Lógica de Acciones (PHP - Método POST)

El script maneja tres tipos de acciones enviadas a través de formularios `POST`:

1.  **Desactivar Período (`name="desactivar"`)**:
    *   **Propósito**: Cambia el estado de un período específico a inactivo.
    *   **Acción**: Ejecuta una consulta `UPDATE` para establecer `activo = FALSE` para el `id` del período proporcionado.

2.  **Activar Período (`name="activar"`)**:
    *   **Propósito**: Establece un período específico como el período escolar activo del sistema, asegurando que solo uno esté activo a la vez.
    *   **Acción**: Esta operación se realiza dentro de una **transacción de base de datos** para garantizar la atomicidad:
        *   Primero, se ejecuta un `UPDATE` para establecer `activo = FALSE` para *todos* los períodos existentes.
        *   Luego, se ejecuta un segundo `UPDATE` para establecer `activo = TRUE` para el `id` del período seleccionado.
        *   Finalmente, la transacción se confirma (`commit`).

3.  **Crear Nuevo Período (`name="crear"`)**:
    *   **Propósito**: Registra un nuevo período escolar en la base de datos.
    *   **Validación**: Antes de la inserción, el script verifica si ya existe un período activo. Si es así, **impide la creación** y muestra un mensaje de error, ya que la política del sistema es que solo puede haber un período activo y no se puede crear uno nuevo mientras otro esté en curso.
    *   **Acción**: Si no hay un período activo, ejecuta una consulta `INSERT` para añadir el nuevo período con su nombre, fecha de inicio y fecha de fin.

En todos los casos, se establece una variable `$mensaje` para proporcionar feedback al usuario sobre el resultado de la operación.

---

## 3. Estructura de la Interfaz (HTML)

La página presenta un diseño de dos paneles:

*   **Panel Izquierdo ("Crear Período Escolar")**:
    *   Contiene un formulario para introducir el nombre, fecha de inicio y fecha de fin de un nuevo período.
    *   Este formulario se muestra **condicionalmente**: solo es visible si no hay ningún período escolar activo en el sistema, reforzando la regla de negocio de que no se pueden crear nuevos períodos mientras uno esté en curso.

*   **Panel Derecho ("Períodos Registrados")**:
    *   Muestra una lista de todos los períodos escolares existentes.
    *   Para cada período, indica su nombre y si está `(Activo)`.
    *   Para los usuarios `master` o `admin`, se muestran botones de acción (`Activar` o `Desactivar`) junto a cada período, permitiendo cambiar su estado.
