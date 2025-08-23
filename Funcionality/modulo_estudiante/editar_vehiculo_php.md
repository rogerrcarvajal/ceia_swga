# Documentación del Archivo: `pages/editar_vehiculo.php`

## 1. Propósito del Archivo

Este archivo proporciona el formulario y la lógica de negocio para **editar un registro de vehículo existente**. Es la página a la que se llega cuando un administrador hace clic en el enlace "Editar" de la lista de vehículos en la página `registro_vehiculos.php`.

Al igual que otras páginas de este módulo, es autocontenida: maneja la carga de datos con el método GET y el procesamiento de la actualización con el método POST.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Carga Inicial de la Página (Método GET)

1.  **Recepción y Validación de ID**: El script espera un `id` numérico de vehículo en la URL. Si no se proporciona o no es válido, redirige al usuario de vuelta a la página principal de registro de vehículos con un mensaje de error.
2.  **Obtención de Datos**: Realiza dos consultas a la base de datos:
    *   Una para obtener todos los datos del vehículo que se está editando, usando el ID proporcionado.
    *   Otra para obtener la lista completa de estudiantes, que se usará para poblar el menú desplegable y permitir reasignar el vehículo a otro estudiante.
3.  **Poblado del Formulario**: Los datos obtenidos del vehículo se utilizan para rellenar los valores por defecto de los campos del formulario (placa, modelo, estudiante seleccionado, estado de autorización).

### b. Procesamiento de la Actualización (Método POST)

Cuando el administrador envía el formulario con los cambios, el script ejecuta lo siguiente:

1.  **Validación de Datos**: Se asegura de que los campos requeridos (estudiante, placa, modelo) no estén vacíos.
2.  **Verificación de Duplicados**: Ejecuta una consulta `SELECT` para comprobar si la nueva placa introducida ya existe en la base de datos, **excluyendo el propio vehículo que se está editando** (`id != :id`). Esto es crucial para permitir guardar sin cambiar la placa, pero evitando duplicados.
3.  **Actualización en Base de Datos**: Si la validación y la comprobación de duplicados son exitosas, ejecuta una consulta `UPDATE` preparada para guardar todos los nuevos valores en el registro del vehículo correspondiente.
4.  **Redirección con Mensaje**: Tras una actualización exitosa, guarda un mensaje de éxito en una variable de sesión (`$_SESSION['mensaje_vehiculo']`) y redirige al usuario de vuelta a `registro_vehiculos.php`. La página de registro leerá y mostrará este mensaje.

---

## 3. Estructura de la Interfaz

*   La interfaz consiste en un único formulario pre-llenado con la información del vehículo a editar.
*   Ofrece un botón "Guardar Cambios" para enviar los datos y un botón "Cancelar" para volver a la página anterior sin guardar.
