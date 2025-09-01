
# Documentación del Módulo de Estudiantes

## 1. Propósito del Módulo

El Módulo de Estudiantes es el núcleo del sistema de gestión académica. Permite administrar de forma integral toda la información relacionada con los estudiantes, desde su inscripción inicial hasta la gestión de sus datos personales, familiares, médicos y su vinculación con los períodos escolares.

**Nota sobre Control de Acceso:** Las funcionalidades de gestión de estudiantes (inscripción y administración de expedientes) están restringidas a usuarios con rol `master` o `admin`. Los usuarios con otros roles no podrán acceder a estas secciones.

## 2. Flujo de Trabajo y Componentes

El módulo se compone de varias páginas y APIs que trabajan en conjunto. El punto de entrada es el **Menú de Gestión de Estudiantes** (`pages/menu_estudiantes.php`).

### a. Inscripción de Nuevos Estudiantes

*   **Página**: `pages/planilla_inscripcion.php`
*   **Propósito**: Ofrece un formulario completo para registrar a un nuevo estudiante en el sistema. Este formulario captura no solo los datos del estudiante, sino también la información de sus padres y su ficha médica básica.
*   **Lógica**: Al enviar el formulario, los datos son procesados por un script (no visible en el análisis actual, pero probablemente una API dedicada) que crea los registros correspondientes en las tablas `estudiantes`, `padres`, `madres` y `fichas_medicas` de la base de datos.
*   **Validación de Entrada**: El formulario implementa validación tanto en el lado del cliente (mediante atributos `pattern` en los campos HTML) como en el lado del servidor (utilizando funciones de sanitización y validación en PHP). Esto asegura la integridad de los datos y previene la inserción de caracteres no permitidos o maliciosos.

### b. Gestión Integral de Expedientes

Esta es la funcionalidad principal del módulo, donde los administradores pasan la mayor parte del tiempo.

*   **Página Principal**: `pages/administrar_planilla_estudiantes.php`
*   **Archivos Involucrados**:
    *   `public/js/admin_estudiantes.js` (Lógica del Frontend)
    *   `api/obtener_estudiante.php` (API para obtener datos del estudiante)
    *   `api/obtener_padre.php` (API para obtener datos del padre)
    *   `api/obtener_madre.php` (API para obtener datos de la madre)
    *   `api/obtener_ficha_medica.php` (API para obtener datos de la ficha médica)
    *   `api/actualizar_estudiante.php` y las APIs de actualización correspondientes para padre, madre y ficha.
*   **Validación de Entrada**: Los formularios de actualización de datos (estudiante, padre, madre, ficha médica) implementan validación en el lado del cliente (mediante atributos `pattern` en los campos HTML) y en el lado del servidor (utilizando funciones de sanitización y validación en PHP). Esto asegura la integridad de los datos y previene la inserción de caracteres no permitidos o maliciosos.

*   **Flujo de Operación**:
    1.  **Visualización**: La página muestra una lista de todos los estudiantes registrados. Un campo de búsqueda permite filtrar la lista dinámicamente para encontrar a un estudiante específico rápidamente.
    2.  **Selección**: El administrador hace clic en el nombre de un estudiante de la lista.
    3.  **Carga de Datos (AJAX)**: El archivo `admin_estudiantes.js` intercepta el clic. De forma asíncrona (sin recargar la página), realiza múltiples llamadas `fetch` a las APIs `obtener_*.php` para traer toda la información del estudiante, su padre, su madre y su ficha médica.
    4.  **Presentación de Datos**: La información recuperada se utiliza para rellenar cuatro formularios distintos que se muestran en la parte derecha de la pantalla.
    5.  **Edición y Guardado**: El administrador puede modificar los datos en cualquiera de los cuatro formularios y presionar el botón "Guardar Cambios" correspondiente a ese formulario.
    6.  **Actualización (AJAX)**: `admin_estudiantes.js` de nuevo intercepta el envío del formulario. Envía los datos actualizados a la API `actualizar_*.php` correspondiente mediante una solicitud `POST`.
    7.  **Confirmación**: La API procesa la actualización en la base de datos y devuelve un mensaje de éxito o error, que se muestra al administrador en la pantalla.

### c. Asignación a Períodos Escolares

Esta funcionalidad permite vincular a un estudiante con un período escolar activo y definir qué grado cursará.

*   **Páginas**: `pages/lista_gestion_estudiantes.php` y `pages/gestionar_estudiantes.php`
*   **Flujo de Operación**:
    1.  El administrador accede a `lista_gestion_estudiantes.php`, que muestra una lista de todos los estudiantes.
    2.  Al seleccionar un estudiante, es redirigido a `gestionar_estudiantes.php` con el ID del estudiante en la URL.
    3.  Esta página muestra los datos del estudiante y le permite, a través de un checkbox y un menú desplegable, asignarlo al período escolar que esté marcado como "activo" en el sistema y seleccionar el grado a cursar.
    4.  Al guardar, el sistema crea o actualiza un registro en la tabla `estudiante_periodo`, que es la que vincula a los estudiantes con los períodos.

### d. Gestión de Vehículos Autorizados

*   **Página**: `pages/registro_vehiculos.php`
*   **Propósito**: Aunque es una entidad separada, se gestiona desde el menú de estudiantes, ya que los vehículos están directamente relacionados con ellos. Esta sección permite registrar los vehículos autorizados para recoger a un estudiante.
