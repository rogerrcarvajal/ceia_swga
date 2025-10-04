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
  - `generado_por_usuario_id`: Registra qué usuario del sistema generó el permiso (auditoría).
  - `fecha_creacion`: Timestamp de cuándo se creó el registro.

### 2.2. Interfaz de Usuario (Frontend)

- **`pages/planilla_salida.php`**: 
  - **Función**: Es el formulario principal para crear una nueva autorización de salida.
  - **Lógica**: Carga dinámicamente la lista de estudiantes inscritos en el período escolar activo. Presenta campos para la fecha, hora, nombre de la persona que retira, parentesco y el motivo. Al enviar el formulario, los datos se postean al endpoint `guardar_autorizacion_salida.php`.

- **`pages/consultar_planilla_salida.php`**: 
  - **Función**: Permite al personal administrativo visualizar las autorizaciones generadas.
  - **Lógica**: Contiene un filtro por semana. Al seleccionar una semana, se realiza una petición asíncrona (Fetch API) al endpoint `consultar_salidas.php`. Los resultados se muestran en una tabla con toda la información relevante de las salidas de esa semana.

### 2.3. Lógica de Negocio (Backend)

- **`api/guardar_autorizacion_salida.php`**: 
  - **Función**: Recibe los datos del formulario de creación.
  - **Lógica**: Valida y sanea los datos recibidos. Utiliza una sentencia preparada de PDO para insertar un nuevo registro en la tabla `autorizaciones_salida` de forma segura. Una vez guardado el registro, obtiene el ID de la nueva autorización y redirige al usuario al script de generación de PDF, pasándole este ID.

- **`api/consultar_salidas.php`**: 
  - **Función**: Sirve los datos a la página de consulta.
  - **Lógica**: Recibe un parámetro `semana` (en formato `YYYY-Www`). Calcula las fechas de inicio y fin de dicha semana. Realiza una consulta a la base de datos para obtener todas las autorizaciones dentro de ese rango de fechas, uniendo la tabla `estudiantes` para obtener el nombre completo. Finalmente, devuelve los resultados en formato JSON.

### 2.4. Generación de Reportes

- **`src/reports_generators/generar_pdf_salida.php`**: 
  - **Función**: Genera el comprobante físico de la autorización.
  - **Lógica**: Recibe el `id` de la autorización a través de la URL. Consulta la base de datos para obtener todos los detalles de ese registro específico. Utiliza la librería FPDF para maquetar un documento PDF a formato media página carta, con el logo de la institución y toda la información de la salida, listo para ser impreso y firmado.

## 3. Flujo de Trabajo

1.  El usuario accede a "Gestionar Autorización de Salida" desde el menú de estudiantes.
2.  Se carga el formulario (`planilla_salida.php`) con la lista de estudiantes.
3.  El usuario completa los datos de la salida y presiona "Guardar y Generar PDF".
4.  El backend (`guardar_autorizacion_salida.php`) almacena la información en la base de datos.
5.  El sistema redirige al generador de PDF (`generar_pdf_salida.php`), que muestra el documento en el navegador.
6.  El usuario puede imprimir este PDF para el archivo físico.
7.  En cualquier momento, el usuario puede ir a la opción "Consultar Salidas" para ver un historial de las autorizaciones por semana.
