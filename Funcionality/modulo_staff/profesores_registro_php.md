# Documentación del Archivo: `pages/profesores_registro.php`

## 1. Propósito del Archivo

Este archivo es la página principal para la gestión de personal (denominado `profesores` en la base de datos, pero referido como "Staff" en la interfaz). Cumple una doble función esencial:

1.  **Registro**: Presenta un formulario para dar de alta a nuevos miembros del personal en el sistema (docentes, administrativos, etc.).
2.  **Visualización**: Muestra una lista completa de todo el personal registrado, indicando su estado de asignación dentro del período escolar activo.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Registro de Personal (Método POST)

Cuando un administrador rellena y envía el formulario de "Registrar Nuevo Ingreso":

1.  **Recepción de Datos**: El script captura los datos del nuevo miembro del personal (nombre, cédula, teléfono, email y categoría) desde la variable `$_POST`.
2.  **Verificación de Duplicados**: Antes de insertar, se realiza una consulta `SELECT` para verificar si ya existe un registro en la tabla `profesores` con el mismo número de `cedula`. Esta es una validación crucial para evitar duplicados de personal.
3.  **Inserción en Base de Datos**: Si la cédula es única, el script ejecuta una consulta `INSERT` preparada para crear el nuevo registro en la tabla `profesores`.
4.  **Feedback al Usuario**: Se genera un mensaje (`$mensaje`) para informar al administrador si el registro fue exitoso o si la cédula ya existía.

### b. Lógica de Carga y Visualización (Método GET)

En cada carga de la página, el script prepara la información que se va a mostrar en los dos paneles:

*   **Panel Izquierdo (Formulario)**:
    *   Se define un array estático (`$categorias_staff`) que contiene las posibles categorías de personal. Este array se usa para poblar dinámicamente el menú desplegable de "Categoría" en el formulario de registro.

*   **Panel Derecho (Listado)**:
    *   **Consulta Maestra**: Se ejecuta una consulta SQL avanzada que utiliza un `LEFT JOIN` entre la tabla `profesores` y la tabla `profesor_periodo`.
    *   **Lógica del JOIN**: Este `LEFT JOIN` es la clave de la funcionalidad. Permite traer a **todos** los profesores y, si tienen una asignación en el período activo, trae también los datos de esa asignación (como el `id` de la asignación y la `posicion`). Si un profesor no está asignado a ese período, esos campos simplemente serán `NULL`.
    *   **Renderizado Condicional**: En el HTML, se itera sobre los resultados de la consulta. Para cada profesor, se comprueba si el campo `asignacion_id` es nulo o no. Basado en esto, se muestra un mensaje condicional:
        *   Si no es nulo: Se muestra "Asignado como: [Posición]" en color verde.
        *   Si es nulo: Se muestra "No asignado a este período" en color amarillo.
    *   **Enlace de Gestión**: Cada elemento de la lista incluye un enlace "Gestionar" que apunta a `pages/gestionar_profesor.php`, pasando el `id` del profesor en la URL para su futura edición o asignación.

---

## 3. Nota sobre Nomenclatura

Es importante destacar que, aunque la interfaz de usuario se refiere a "Staff" y "Personal", las tablas subyacentes en la base de datos utilizan el término `profesores` (`profesores`, `profesor_periodo`) para referirse a todos los miembros del personal, sin importar su categoría.
