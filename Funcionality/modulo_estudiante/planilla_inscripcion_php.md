# Documentación del Archivo: `pages/planilla_inscripcion.php`

## 1. Propósito del Archivo

Este archivo es una de las piezas más complejas y cruciales del sistema. Cumple un doble propósito:

1.  **Presentación de Interfaz**: Muestra al usuario un formulario de inscripción exhaustivo, dividido en cuatro secciones: datos del estudiante, del padre, de la madre y la ficha médica.
2.  **Procesamiento de Datos (Backend)**: Contiene toda la lógica PHP necesaria para recibir los datos de este formulario, validarlos y crear de forma segura y transaccional los registros correspondientes en 5 tablas diferentes de la base de datos (`padres`, `madres`, `estudiantes`, `salud_estudiantil`, `estudiante_periodo`).

Además, incluye JavaScript embebido para una experiencia de usuario mejorada al momento de registrar a los padres.

---

## 2. Lógica de Negocio y Flujo de Operación (PHP)

### a. Carga Inicial de la Página (Método GET)

*   **Control de Acceso**: Valida la sesión del usuario y restringe el acceso a roles `master` o `admin`.
*   **Verificación de Período**: Comprueba que exista un período escolar activo. Si no lo hay, muestra un mensaje de error, ya que es un requisito para poder realizar una inscripción.

### b. Procesamiento del Formulario (Método POST)

Esta es la lógica principal del archivo, que se ejecuta cuando el administrador llena y envía el formulario.

*   **Transacción de Base de Datos**: Toda la operación de inserción está envuelta en una transacción (`$conn->beginTransaction()`). Esto es una medida de seguridad crítica que asegura la **atomicidad** de la inscripción: o todos los registros se guardan correctamente, o no se guarda ninguno. Si ocurre un error en cualquier punto, se ejecuta un `rollBack()` para deshacer todos los cambios, evitando datos corruptos o incompletos.

*   **Gestión Inteligente de Representantes (Padre y Madre)**:
    1.  El script primero verifica si el usuario vinculó un representante existente usando la búsqueda del frontend (a través del campo oculto `padre_id_existente` o `madre_id_existente`).
    2.  Si no se vinculó uno existente y se proporcionó una cédula, el script **busca en la base de datos** si ya existe un representante con esa cédula.
    3.  Si la cédula ya existe, reutiliza el ID de ese representante para la nueva inscripción.
    4.  Si la cédula no existe, procede a **insertar un nuevo registro** en la tabla `padres` o `madres` y obtiene el ID del nuevo registro (`lastInsertId()`).
    *Esta lógica previene la duplicación de registros de representantes en el sistema.*

*   **Inserción del Estudiante**: Con los IDs del padre y la madre ya resueltos (sean nuevos o existentes), el script inserta los datos del estudiante en la tabla `estudiantes`.

*   **Inserción de la Ficha Médica**: Utiliza el ID del estudiante recién creado (`lastInsertId()`) para insertar el registro correspondiente en la tabla `salud_estudiantil`.

*   **Asignación al Período Escolar**: Si el administrador marcó la casilla "Activo" (no visible en el código HTML proporcionado, pero manejado en el backend), se crea un registro en la tabla `estudiante_periodo` para vincular al nuevo estudiante con el período escolar activo y el grado cursado.

*   **Commit o Rollback**: Si todas las inserciones son exitosas, la transacción se confirma con `$conn->commit()`. Si algo falla, se revierte con `$conn->rollBack()` y se muestra un mensaje de error detallado.

---

## 3. Lógica del Frontend (JavaScript Embebido)

El archivo contiene un bloque `<script>` que mejora la usabilidad del formulario de representantes.

*   **Evento `blur`**: Cuando el administrador termina de escribir en el campo de cédula del padre o de la madre y sale del campo, se dispara el evento `blur`.
*   **Función `buscarRepresentante(tipo)`**: Esta función asíncrona se activa con el evento `blur`.
    1.  Toma la cédula introducida.
    2.  Realiza una llamada `fetch` a la API `api/buscar_representante.php`.
    3.  Muestra el resultado debajo del campo de cédula:
        *   **Si se encuentra**: Muestra el nombre del representante y dos botones: "Vincular" e "Ignorar y Registrar Nuevo".
        *   **Si no se encuentra**: Muestra un mensaje informativo.
*   **Función `vincularRepresentante(...)`**: Si el usuario hace clic en "Vincular", esta función guarda el ID del representante existente en el campo oculto (`padre_id_existente`), deshabilita los demás campos del formulario de ese representante (para evitar edición accidental) y muestra un mensaje de confirmación.
*   **Función `ignorarBusqueda(...)`**: Si el usuario decide registrarlo como nuevo, esta función limpia los campos ocultos y se asegura de que los campos del formulario estén habilitados.
