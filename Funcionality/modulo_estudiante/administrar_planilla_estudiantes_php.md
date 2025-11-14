# Documentación del Archivo: `pages/administrar_planilla_estudiantes.php`

## 1. Propósito del Archivo

Este archivo PHP constituye la **interfaz principal** para la gestión integral de los expedientes de los estudiantes. Su función es presentar al usuario administrador una vista de dos paneles que permite, por un lado, buscar y seleccionar a un estudiante y, por el otro, ver y modificar toda su información detallada (datos personales, familiares y médicos).

Este archivo se encarga únicamente de la **presentación inicial** de la página y de cargar la lista de estudiantes. Toda la interactividad, la carga de datos de un estudiante específico y las actualizaciones se delegan al archivo JavaScript `public/js/admin_estudiantes.js`.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

Cuando un usuario accede a esta página, el script del lado del servidor ejecuta los siguientes pasos:

1.  **Inicio de Sesión y Control de Acceso**: Verifica que haya una sesión de usuario activa y que el rol del usuario sea `master` o `admin`. Si no se cumplen estas condiciones, redirige al usuario a la página de inicio de sesión o al dashboard con un mensaje de acceso denegado.

2.  **Inclusión de Configuración**: Importa el archivo `src/config.php` para establecer la conexión con la base de datos (`$conn`).

3.  **Verificación de Período Activo**: Realiza una consulta a la tabla `periodos_escolares` para obtener el nombre del período que está actualmente activo. Este nombre se muestra en la parte superior de la página como referencia contextual para el administrador.

4.  **Carga de la Lista de Estudiantes**: Ejecuta una consulta (`SELECT`) a la tabla `estudiantes` para obtener el `id`, `nombre_completo` y `apellido_completo` de **todos** los estudiantes registrados. Los resultados se ordenan alfabéticamente por apellido y luego por nombre.

---

## 3. Estructura de la Interfaz (HTML)

El cuerpo (`<body>`) del archivo está dividido en dos componentes principales dentro de un `main-container`:

### a. Panel Izquierdo (`left-panel`)

*   **Propósito**: Mostrar la lista completa de estudiantes y permitir su filtrado.
*   **Componentes**:
    *   Un campo de texto (`<input type="text" id="filtro_estudiantes">`) que el usuario utilizará para buscar estudiantes.
    *   Una lista no ordenada (`<ul id="lista_estudiantes">`) que se rellena dinámicamente con los nombres de los estudiantes obtenidos en el paso 4 de la lógica de carga. Cada elemento de la lista (`<li>`) tiene un atributo `data-id` que almacena el ID único del estudiante, el cual es crucial para la interactividad del frontend.

### b. Panel Derecho (`right-panel`)

*   **Propósito**: Mostrar la información detallada del estudiante seleccionado y permitir su edición.
*   **Estado Inicial**: Por defecto, este panel solo muestra un mensaje: *"Seleccione un estudiante de la lista para ver su expediente."* (`<div id="panel_informativo">`). Los formularios de datos están ocultos (`display:none;`).
*   **Contenedor de Formularios**: El `div` con `id="panel_datos_estudiante"` contiene cuatro formularios HTML distintos:
    1.  `form_estudiante`: Para los datos personales del estudiante.
    2.  `form_padre`: Para los datos del padre.
    3.  `form_madre`: Para los datos de la madre.
    4.  `form_ficha_medica`: Para la información de salud del estudiante.

    Cada campo de estos formularios tiene un `id` único (ej. `id="nombre_completo"`) que será utilizado por JavaScript para rellenarlo con los datos correspondientes.

---

## 4. Vínculo con el Frontend

Al final del archivo, se incluye el script que dota de funcionalidad a la página:

```html
<script src="/ceia_swga/public/js/admin_estudiantes.js"></script>
```

Este script es responsable de:
*   Implementar la funcionalidad de filtrado en la lista de estudiantes.
*   Detectar cuándo un usuario hace clic en un estudiante de la lista.
*   Realizar las llamadas AJAX a las APIs para obtener y mostrar los datos en los formularios del panel derecho.
*   Manejar el envío de los formularios para actualizar la información.
