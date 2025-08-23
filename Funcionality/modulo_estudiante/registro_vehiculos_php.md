# Documentación del Archivo: `pages/registro_vehiculos.php`

## 1. Propósito del Archivo

Este archivo proporciona una interfaz de gestión (CRUD - Crear, Leer, Actualizar, Eliminar) para los vehículos autorizados asociados a los estudiantes. Permite a los administradores registrar nuevos vehículos, ver una lista de los vehículos existentes y acceder a las opciones para modificarlos o eliminarlos.

La página funciona de manera autocontenida, manejando tanto la visualización como el procesamiento del registro con lógica PHP en el mismo archivo.

---

## 2. Lógica de Negocio y Flujo de Operación

La página está dividida en dos funcionalidades principales que se ejecutan en el servidor.

### a. Lógica de Registro de Vehículo (Método POST)

Cuando un administrador envía el formulario para registrar un nuevo vehículo, el script ejecuta la siguiente lógica:

1.  **Validación de Datos**: Comprueba que se haya seleccionado un estudiante y que los campos de placa y modelo no estén vacíos.
2.  **Verificación de Duplicados**: Realiza una consulta `SELECT` a la tabla `vehiculos` para asegurarse de que la placa introducida no exista ya en el sistema. Esta es una restricción clave para mantener la integridad de los datos.
3.  **Inserción en la Base de Datos**: Si la placa es única, el script ejecuta una consulta `INSERT` preparada para añadir el nuevo registro a la tabla `vehiculos`, incluyendo el `estudiante_id` que lo asocia, la placa, el modelo y su estado de autorización.
4.  **Feedback al Usuario**: Almacena un mensaje de éxito o error en la variable `$mensaje` para ser mostrado en la interfaz.

### b. Lógica de Visualización (Método GET)

En cada carga de la página, el script prepara la información que se va a mostrar:

1.  **Población del Formulario**: Obtiene una lista completa de todos los estudiantes de la tabla `estudiantes` y la utiliza para rellenar el menú desplegable en el formulario de registro, permitiendo al administrador asociar el nuevo vehículo con un estudiante.
2.  **Listado de Vehículos Registrados**: 
    *   Realiza una consulta `SELECT` que une (`JOIN`) las tablas `vehiculos` y `estudiantes` para obtener una lista de todos los vehículos junto con el nombre del estudiante al que están asociados.
    *   Muestra esta información en una lista en el panel derecho de la página.
    *   **Nota**: El archivo contiene una lógica de paginación que actualmente está implementada de forma incorrecta (obtiene todos los resultados en lugar de solo los de la página actual). Esto es un punto de mejora técnica.
3.  **Navegación para Edición/Eliminación**: Para cada vehículo en la lista, genera dinámicamente dos enlaces:
    *   Un enlace "Editar" que apunta a `pages/editar_vehiculo.php?id=[ID_VEHICULO]`.
    *   Un enlace "Eliminar" que apunta a `pages/eliminar_vehiculo.php?id=[ID_VEHICULO]`, el cual incluye una confirmación simple de JavaScript (`onclick="return confirm(...)"`).

---

## 3. Estructura de la Interfaz

La página se presenta en un formato de dos paneles:

*   **Panel Izquierdo**: Contiene el formulario de "Registrar Nuevo Vehículo".
*   **Panel Derecho**: Muestra la lista de "Vehículos Autorizados" con sus respectivos enlaces de gestión.
