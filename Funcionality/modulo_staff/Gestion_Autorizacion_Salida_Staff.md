# Módulo: Gestión de Autorización de Salida de Personal (Staff)

## 1. Propósito General

El módulo de **Gestión de Autorización de Salida de Personal** está diseñado para digitalizar y formalizar el proceso de solicitud y aprobación de permisos de salida para los empleados (docentes y administrativos) de la institución durante la jornada laboral.

Su objetivo es proporcionar un registro centralizado, auditable y fácil de gestionar, mejorando el control y la organización de las ausencias temporales del personal.

---

## 2. Componentes y Flujo de Trabajo

El módulo se compone de dos interfaces principales: una para la creación de autorizaciones y otra para su consulta y gestión.

### a. Creación de Autorización (`pages/planilla_salida_staff.php`)

Interfaz para registrar un nuevo permiso de salida para un miembro del personal.

- **Flujo de Usuario:**
    1. El administrador accede a la página.
    2. El script `public/js/planilla_salida_staff.js` se inicializa, estableciendo la fecha actual por defecto.
    3. **Selección de Categoría:** El usuario elige una categoría de personal (ej. "Docente", "Administrativo").
    4. **Carga de Personal:** Al cambiar la categoría, se realiza una llamada a `api/obtener_staff_por_categoria.php` para poblar el selector con los nombres del personal correspondiente.
    5. **Selección de Personal:** Al seleccionar a una persona, los campos "Posición" y "Cédula" se rellenan automáticamente.
    6. **Completar Formulario:** El usuario ingresa la fecha, hora de salida, duración y el motivo del permiso.
    7. **Envío de Formulario:** Al hacer clic en "Guardar Autorización", los datos del formulario se envían a `api/guardar_autorizacion_staff.php`.
    8. **Respuesta y PDF:** Si el guardado es exitoso, la API devuelve el ID del nuevo registro. El frontend habilita el botón "Generar PDF", que permite crear el comprobante físico a través de `src/reports_generators/generar_permiso_staff_pdf.php`.

### b. Consulta y Gestión de Autorizaciones (`pages/gestion_autorizacion_staff.php`)

Esta interfaz, creada para completar la funcionalidad del módulo, permite visualizar y filtrar los permisos registrados.

- **Flujo de Usuario:**
    1. Al cargar la página, el script `public/js/consultar_salidas_staff.js` establece la semana actual y carga los registros correspondientes por defecto.
    2. **Filtros Dinámicos:** El usuario puede filtrar los registros por semana, categoría de personal o por un empleado específico.
    3. **Llamada a la API:** Cada cambio en los filtros dispara una llamada `fetch` a `api/consultar_salidas_staff.php`.
    4. **Visualización de Resultados:** La API devuelve los registros en formato JSON, y el script los renderiza dinámicamente en la tabla de resultados.

---

## 3. Componentes Técnicos Detallados

### a. Scripts de Cliente (JavaScript)

- **`public/js/planilla_salida_staff.js`**
    - **Responsabilidad:** Gestiona la lógica de la página de creación (`planilla_salida_staff.php`).
    - **Funciones Clave:** Carga dinámica de personal por categoría, autocompletado de campos, envío del formulario vía `fetch` y manejo del botón de generación de PDF.

- **`public/js/consultar_salidas_staff.js`**
    - **Responsabilidad:** Controla la interactividad de la página de consulta (`gestion_autorizacion_staff.php`).
    - **Funciones Clave:** Carga inicial de datos, actualización dinámica del filtro de personal, y renderizado de la tabla de resultados basado en las respuestas de la API.

### b. APIs Asociadas (Backend)

- **`api/guardar_autorizacion_staff.php`**
    - **Método:** `POST`
    - **Descripción:** Recibe los datos del formulario y los inserta en la tabla `autorizaciones_salida_staff`. Utiliza sentencias preparadas para garantizar la seguridad.
    - **Respuesta (Éxito):** JSON `{ "status": "exito", "id": ID_NUEVO, "mensaje": "..." }`

- **`api/obtener_staff_por_categoria.php`**
    - **Método:** `GET`
    - **Descripción:** Devuelve una lista de miembros del personal que pertenecen a una categoría específica y están asignados al período activo.
    - **Parámetros (URL):** `?categoria=NOMBRE_CATEGORIA`
    - **Respuesta:** Array JSON de objetos de personal.

- **`api/consultar_salidas_staff.php`**
    - **Método:** `GET`
    - **Descripción:** Proporciona una lista de autorizaciones de salida del personal basada en filtros de semana, categoría y/o ID de empleado.
    - **Parámetros (URL):** `?semana=YYYY-Www&categoria=...&staff_id=...`
    - **Respuesta:** Objeto JSON `{ "status": "exito", "registros": [...] }`.

### c. Generadores de Reportes

- **`src/reports_generators/generar_permiso_staff_pdf.php`**
    - **Descripción:** Genera un documento PDF formal para una única solicitud de permiso, obteniendo los datos a través del ID proporcionado en la URL.

### d. Base de Datos

- **Tabla Principal:** `autorizaciones_salida_staff`
- **Columnas Clave:** `id`, `profesor_id`, `periodo_id`, `registrado_por_usuario_id`, `fecha_permiso`, `hora_salida`, `duracion_horas`, `motivo`.
