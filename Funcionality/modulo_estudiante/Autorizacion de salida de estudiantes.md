# Funcionalidad del Módulo de Autorización de Salida de Estudiantes

## 1. Propósito General

El Módulo de Autorización de Salida de Estudiantes fue diseñado para automatizar y digitalizar el proceso de registro y control de las salidas tempranas de los estudiantes durante el horario escolar. Reemplaza el llenado de planillas manuales, centralizando la información en la base de datos del SWGA, mejorando la seguridad, la eficiencia y la capacidad de auditoría.

## 2. Componentes del Módulo

El módulo se compone de varios archivos que trabajan en conjunto para ofrecer una solución integral.

### 2.1. Base de Datos

- **Tabla `autorizaciones_salida`**: Es el corazón del módulo. Almacena cada registro de salida autorizada con los siguientes campos clave:
  - `id`: Identificador único de la autorización.
  - `estudiante_id`: Vincula el registro con el estudiante correspondiente.
  - `fecha_salida` y `hora_salida`: Registran el momento exacto de la salida.
  - `retirado_por_nombre` y `retirado_por_parentesco`: Guardan la información de la persona que retira al estudiante.
  - `motivo`: Describe la razón de la salida temprana.
  - `registrado_por_usuario_id`: Registra qué usuario del sistema generó el permiso (auditoría).
  - `fecha_creacion`: Timestamp de cuándo se creó el registro.

### 2.2. Interfaz de Usuario (Frontend)

- **`pages/planilla_salida.php`**: 
  - **Función**: Es el formulario principal para crear una nueva autorización de salida.
  - **Lógica**: Carga dinámicamente la lista de estudiantes inscritos en el período escolar activo. Presenta campos para la fecha, hora, nombre de la persona que retira, parentesco y el motivo. Al enviar el formulario, los datos se envían a la API `api/guardar_planilla_salida.php`.

- **`pages/gestion_planilla_salida.php`**: 
  - **Función**: Permite al personal administrativo visualizar y gestionar las autorizaciones generadas.
  - **Lógica**: Contiene filtros por semana y por estudiante. Al seleccionar una semana, se realiza una petición asíncrona (Fetch API) al endpoint `api/consultar_salidas.php`. Los resultados se muestran en una tabla. Además, incluye un botón para generar un reporte en PDF de las salidas de la semana seleccionada.

### 2.3. Lógica de Negocio (Backend)

- **`api/guardar_planilla_salida.php`**: 
  - **Función**: Recibe los datos del formulario de creación.
  - **Lógica**: Valida y sanea los datos recibidos. Utiliza una sentencia preparada de PDO para insertar un nuevo registro en la tabla `autorizaciones_salida` de forma segura. Una vez guardado el registro, obtiene el ID de la nueva autorización y redirige al usuario al script de generación de PDF, pasándole este ID.

- **`api/consultar_salidas.php`**: 
  - **Función**: Sirve los datos a la página de gestión.
  - **Lógica**: Recibe un parámetro `semana` y opcionalmente `estudiante_id`. Calcula las fechas de inicio y fin de dicha semana. Realiza una consulta a la base de datos para obtener todas las autorizaciones dentro de ese rango de fechas, uniendo la tabla `estudiantes` para obtener el nombre completo. Finalmente, devuelve los resultados en formato JSON.

### 2.4. Generación de Reportes

- **`src/reports_generators/generar_pdf_salida.php`**: 
  - **Función**: Genera el comprobante físico de una autorización individual.
  - **Lógica**: Recibe el `id` de la autorización a través de la URL. Consulta la base de datos para obtener todos los detalles de ese registro específico. Utiliza la librería FPDF para maquetar un documento PDF a formato media página carta, con el logo de la institución y toda la información de la salida, listo para ser impreso y firmado.

- **`src/reports_generators/generar_reporte_salidas.php`**:
  - **Función**: Genera un reporte en PDF con todas las salidas de una semana específica.
  - **Lógica**: Recibe los parámetros `semana` y `estudiante_id` (opcional) desde la URL. Realiza una consulta a la base de datos para obtener todos los registros que coincidan con los filtros. Utiliza la librería FPDF para generar un reporte tabular en formato carta horizontal, mostrando todas las salidas de la semana.

## 3. Flujo de Trabajo

1.  El usuario accede a "Planilla de Salida" desde el menú de estudiantes para crear una nueva autorización.
2.  Se carga el formulario (`planilla_salida.php`) con la lista de estudiantes.
3.  El usuario completa los datos de la salida y presiona "Guardar y Generar PDF".
4.  El backend (`api/guardar_planilla_salida.php`) almacena la información en la base de datos.
5.  El sistema redirige al generador de PDF (`src/reports_generators/generar_pdf_salida.php`), que muestra el documento en el navegador.
6.  El usuario puede imprimir este PDF para el archivo físico.
7.  Para ver un historial, el usuario accede a "Gestionar Salidas". En esta pantalla (`pages/gestion_planilla_salida.php`), puede filtrar por semana y/o estudiante para ver las autorizaciones.
8.  Desde la pantalla de gestión, el usuario puede generar un reporte semanal en PDF de todas las salidas.