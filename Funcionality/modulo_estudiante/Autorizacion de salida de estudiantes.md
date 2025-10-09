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
        - Si se elige "Padre", `gestion_salidas.js` llama a `api/obtener_padre.php`.
        - Si se elige "Madre", `gestion_salidas.js` llama a `api/obtener_madre.php`.
        - Si se elige "Otro", se muestra un formulario para ingresar los datos manualmente.
    6. **Envío de Formulario:** Al hacer clic en "Guardar", el formulario se envía a `api/guardar_planilla_salida.php`.
    7. **Respuesta:** Si la autorización se guarda con éxito, la API devuelve el ID del nuevo registro. El frontend (`gestion_salidas.js`) recibe este ID y habilita el botón "Generar PDF".
    8. **Generación de PDF:** Al hacer clic en "Generar PDF", se abre una nueva pestaña apuntando a `src/reports_generators/generar_autorizacion_pdf.php?id={ID_DE_SALIDA}`, que genera el documento formal.

### b. Consulta y Gestión de Planillas (`pages/gestion_planilla_salida.php`)

Interfaz para buscar, visualizar y gestionar autorizaciones históricas y actuales.

- **Flujo de Usuario:**
    1. Al cargar la página, el script `public/js/consultar_salidas.js` establece la semana actual en el filtro de fecha por defecto.
    2. **Disparador de Búsqueda:** Cada vez que el usuario cambia la semana o selecciona un estudiante, se activa la función `cargarResultados()`.
    3. **Llamada a la API:** La función realiza una petición `fetch` a `api/consultar_salidas.php` con los parámetros de semana y estudiante.
    4. **Visualización de Resultados:** La API devuelve un listado de registros en formato JSON, que el script utiliza para construir y mostrar las filas en la tabla de resultados.
    5. **Generación de Reporte:** Un botón permite generar un PDF con los resultados filtrados a través de `src/reports_generators/generar_reporte_salidas.php`.

---

## 3. Componentes Técnicos Detallados

### a. Scripts de Cliente (JavaScript)

- **`public/js/gestion_salidas.js`**
    - **Responsabilidad:** Orquesta la lógica de la página de creación (`planilla_salida.php`).
    - **Funciones Clave:** Carga de estudiantes, obtención de datos de padres/madres, envío de formulario vía `fetch`, y gestión del botón de PDF.

- **`public/js/consultar_salidas.js`**
    - **Responsabilidad:** Gestiona la interactividad de la página de consulta (`gestion_planilla_salida.php`).
    - **Funciones Clave:** Establece filtros, dispara búsquedas, realiza llamadas `fetch` a la API de consulta y renderiza la tabla de resultados.

### b. APIs Asociadas (Backend)

- **`api/guardar_planilla_salida.php`**
    - **Método:** `POST`
    - **Descripción:** Registra una nueva autorización de salida.
    - **Seguridad:** Incluye una validación crítica para asegurar que el `padre_id` o `madre_id` enviado corresponda con el del estudiante, previniendo inconsistencias de datos.
    - **Respuesta (Éxito):** JSON `{ "success": true, "salida_id": ID_NUEVO }`

- **`api/obtener_estudiantes_por_periodo.php`**
    - **Método:** `GET`
    - **Descripción:** Devuelve una lista de estudiantes asignados a un período escolar.
    - **Respuesta:** Array JSON de objetos de estudiantes.

- **`api/obtener_padre.php` y `api/obtener_madre.php`**
    - **Método:** `GET`
    - **Descripción:** Obtienen los datos del padre o la madre de un estudiante específico.
    - **Parámetros (URL):** `?estudiante_id=ID_DEL_ESTUDIANTE`
    - **Respuesta:** Objeto JSON con los datos del representante.

- **`api/consultar_salidas.php`**
    - **Método:** `GET`
    - **Descripción:** Busca y devuelve registros de autorizaciones de salida basados en filtros.
    - **Parámetros (URL):** `?semana=YYYY-Www&estudiante_id=ID`
    - **Respuesta:** Objeto JSON `{ "status": "exito", "registros": [...] }`.

### c. Generadores de Reportes

- **`src/reports_generators/generar_autorizacion_pdf.php`**: Genera el comprobante individual de una autorización.
- **`src/reports_generators/generar_reporte_salidas.php`**: Genera un reporte tabular con múltiples registros según los filtros aplicados.

### d. Base de Datos

- **Tabla Principal:** `autorizaciones_salida`
- **Columnas Clave:** `id`, `estudiante_id`, `fecha_salida`, `hora_salida`, `retirado_por_nombre`, `retirado_por_parentesco`, `motivo`, `registrado_por_usuario_id`.