# Documentación del Archivo: `pages/gestionar_estudiantes.php`

## 1. Propósito del Archivo

Este archivo es la interfaz de gestión donde un administrador puede matricular, desmatricular o cambiar el grado de un estudiante específico dentro del **período escolar activo**. Funciona como una página de edición que se enfoca en la relación entre un estudiante y el período actual, utilizando la tabla `estudiante_periodo` como nexo.

El propio archivo contiene la lógica para mostrar el estado actual de la asignación y para procesar los cambios enviados por el administrador.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Carga Inicial de la Página (Método GET)

1.  **Recepción de ID**: El script obtiene el `id` del estudiante que se va a gestionar desde el parámetro en la URL (ej. `...?id=V12345678`).
2.  **Obtención de Datos**: Realiza tres consultas a la base de datos para preparar la interfaz:
    *   Busca los datos principales del estudiante (nombre, apellido) en la tabla `estudiantes` para mostrar a quién se está editando.
    *   Identifica el período escolar activo en la tabla `periodos_escolares`.
    *   Verifica si ya existe un registro en la tabla `estudiante_periodo` para el estudiante y el período activo actuales. Esta consulta es clave, ya que su resultado determina si el formulario aparecerá con la casilla marcada y un grado ya seleccionado.
3.  **Renderizado del Formulario**: Muestra el formulario HTML, utilizando los datos de la consulta anterior para pre-configurar los campos (marcar el checkbox y seleccionar el grado si ya existe una asignación).

### b. Procesamiento de Cambios (Método POST)

Cuando el administrador guarda los cambios, el formulario se envía a sí mismo (`action=""`) y se ejecuta la siguiente lógica:

1.  **Verificación del Checkbox**: El script primero comprueba si la casilla `asignar_periodo` fue enviada (`isset($_POST['asignar_periodo'])`).
2.  **Si la casilla está marcada (Asignar/Actualizar)**:
    *   Valida que se haya seleccionado un grado. Si no, muestra un error.
    *   Comprueba si ya existía una asignación previa para ese estudiante en ese período.
    *   Si ya existía, ejecuta una consulta `UPDATE` en `estudiante_periodo` para actualizar el campo `grado_cursado`.
    *   Si no existía, ejecuta una consulta `INSERT` para crear el nuevo registro de asignación.
3.  **Si la casilla no está marcada (Desmatricular)**:
    *   El script interpreta esto como una orden para eliminar la asignación.
    *   Ejecuta una consulta `DELETE` en la tabla `estudiante_periodo` para quitar la fila que vincula al estudiante con el período activo.
4.  **Feedback al Usuario**: En todos los casos, se genera un mensaje de éxito o error (`$mensaje`) que se muestra en la parte superior del formulario para informar al administrador del resultado de la operación.

---

## 3. Lógica del Frontend (JavaScript Embebido)

El archivo incluye un pequeño y eficiente script de JavaScript para mejorar la usabilidad:

*   **Funcionalidad**: El script escucha los cambios en el checkbox `asignar_periodo`.
*   **Acción**: Si el checkbox está marcado, muestra el menú desplegable para seleccionar el grado. Si no está marcado, lo oculta. Esto evita que el usuario vea opciones irrelevantes y hace la interfaz más limpia e intuitiva.
