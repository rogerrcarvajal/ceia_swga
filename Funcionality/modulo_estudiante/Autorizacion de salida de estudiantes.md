# Módulo: Autorización de Salida de Estudiantes

## 1. Propósito General

El módulo de **Autorización de Salida de Estudiantes** es un componente crítico del SWGA diseñado para registrar, controlar y gestionar de forma digital los permisos de salida de los estudiantes de la institución durante el horario escolar.

Su objetivo principal es reemplazar los procesos manuales, ofreciendo una solución centralizada que garantiza la trazabilidad, seguridad y formalidad de cada autorización.

---

## 2. Componentes y Flujo de Trabajo

El módulo integra interfaces de usuario, scripts de cliente (JavaScript) y APIs de backend para ofrecer una funcionalidad cohesiva.

### a. Creación de Planilla de Salida (`pages/planilla_salida.php`)

Es la interfaz principal para registrar una nueva autorización.

- **Flujo de Usuario:**
    1. El personal administrativo abre la página.
    2. El script `public/js/gestion_salidas.js` se activa.
    3. **Carga de Estudiantes:** Se realiza una llamada a `api/obtener_estudiantes_por_periodo.php` para poblar dinámicamente el selector de estudiantes correspondiente al período escolar activo.
    4. **Selección de Estudiante:** Al seleccionar un estudiante, se habilitan las opciones para elegir quién lo retira (Padre, Madre, Otro).
    5. **Selección de Persona Autorizada:**
        - Si se elige "Padre" o "Madre", `gestion_salidas.js` llama a `api/buscar_representante.php` para obtener y mostrar los datos del representante legal.
        - Si se elige "Otro", se muestra un formulario para ingresar los datos manualmente.
    6. **Envío de Formulario:** Al hacer clic en "Guardar", el formulario se envía a `api/guardar_autorizacion_salida.php`.
    7. **Respuesta:** Si la autorización se guarda con éxito, la API devuelve el ID del nuevo registro. El frontend (`gestion_salidas.js`) recibe este ID y habilita el botón "Generar PDF".
    8. **Generación de PDF:** Al hacer clic en "Generar PDF", se abre una nueva pestaña apuntando a `src/reports_generators/generar_autorizacion_pdf.php?id={ID_DE_SALIDA}`, que genera el documento formal.

### b. Consulta de Planillas de Salida (`pages/consultar_planilla_salida.php`)

Interfaz para buscar y visualizar autorizaciones históricas y actuales.

- **Flujo de Usuario:**
    1. Al cargar la página, el script `public/js/consultar_salidas.js` establece la semana actual en el filtro de fecha por defecto.
    2. **Disparador de Búsqueda:** Cada vez que el usuario cambia la semana o selecciona un estudiante, se activa la función `cargarResultados()`.
    3. **Llamada a la API:** La función realiza una petición `fetch` a `api/consultar_salidas.php` con los parámetros de semana y estudiante.
    4. **Visualización de Resultados:** La API devuelve un listado de registros en formato JSON, que el script utiliza para construir y mostrar las filas en la tabla de resultados.

### c. Gestión de Planillas (`pages/gestion_planilla_salida.php`)

Actúa como un portal central para ver todas las autorizaciones registradas, permitiendo al personal administrativo tener una vista general y acceder a acciones rápidas como la reimpresión de un PDF.

---

## 3. Componentes Técnicos Detallados

### a. Scripts de Cliente (JavaScript)

- **`public/js/gestion_salidas.js`**
    - **Responsabilidad:** Orquesta la lógica de la página de creación de planillas (`planilla_salida.php`).
    - **Funciones Clave:**
        - Carga la lista de estudiantes al iniciar.
        - Obtiene y muestra los datos del padre o madre cuando se seleccionan.
        - Maneja el envío del formulario vía `fetch` a la API de guardado.
        - Habilita y gestiona la lógica del botón para generar el PDF.
        - Pre-rellena la fecha y hora actuales para agilizar el proceso.

- **`public/js/consultar_salidas.js`**
    - **Responsabilidad:** Gestiona la interactividad de la página de consulta (`consultar_planilla_salida.php`).
    - **Funciones Clave:**
        - Establece la semana actual como filtro por defecto.
        - Dispara la búsqueda de datos cuando los filtros cambian.
        - Realiza la llamada `fetch` a la API de consulta.
        - Renderiza dinámicamente la tabla de resultados a partir de la respuesta JSON.

### b. APIs Asociadas (Backend)

- **`api/guardar_autorizacion_salida.php`**
    - **Método:** `POST`
    - **Descripción:** Registra una nueva autorización de salida en la base de datos.
    - **Parámetros (FormData):** `estudiante_id`, `periodo_activo_id`, `fecha_salida`, `hora_salida`, `autorizado_por` (padre, madre, otro), datos del autorizado, etc.
    - **Respuesta (Éxito):** JSON `{ "success": true, "salida_id": ID_NUEVO }`
    - **Respuesta (Error):** JSON `{ "success": false, "message": "MOTIVO_DEL_ERROR" }`

- **`api/obtener_estudiantes_por_periodo.php`**
    - **Método:** `GET`
    - **Descripción:** Devuelve una lista de todos los estudiantes asignados a un período escolar específico.
    - **Parámetros (URL):** `?periodo_id=ID_DEL_PERIODO`
    - **Respuesta:** Array JSON de objetos, donde cada objeto es un estudiante con `id`, `nombre_completo`, `apellido_completo`.

- **`api/buscar_representante.php`**
    - **Método:** `GET`
    - **Descripción:** Obtiene los datos del padre y/o madre de un estudiante específico.
    - **Parámetros (URL):** `?estudiante_id=ID_DEL_ESTUDIANTE`
    - **Respuesta:** Objeto JSON `{ "padre": { "id": ..., "nombre_completo": ... }, "madre": { "id": ..., "nombre_completo": ... } }`. Si un representante no existe, su valor es `null`.

- **`api/consultar_salidas.php`**
    - **Método:** `GET`
    - **Descripción:** Busca y devuelve registros de autorizaciones de salida basados en filtros de semana y/o estudiante.
    - **Parámetros (URL):** `?semana=YYYY-Www&estudiante_id=ID_DEL_ESTUDIANTE`
    - **Respuesta:** Objeto JSON `{ "status": "exito", "registros": [...] }` donde `registros` es un array de autorizaciones.

### c. Base de Datos

- **Tabla Principal:** `autorizaciones_salida`
- **Columnas Clave:** `id`, `estudiante_id`, `representante_id`, `fecha_salida`, `hora_salida`, `fecha_retorno`, `hora_retorno`, `observaciones`, `periodo_escolar_id`, `creado_por`, `fecha_creacion`.