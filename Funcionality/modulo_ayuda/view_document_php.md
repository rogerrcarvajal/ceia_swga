# Documentación del Archivo: `pages/view_document.php`

## 1. Propósito del Archivo

Este archivo PHP actúa como el **visor universal de documentos Markdown** para el sistema SWGA. Su propósito es leer dinámicamente cualquier archivo `.md` ubicado dentro del directorio `funcionality/`, convertir su contenido a HTML y presentarlo de manera segura y estilizada dentro de la plantilla visual estándar del sistema. Es el componente clave que permite que toda la documentación granular sea accesible directamente desde la interfaz de usuario.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: Verifica que el usuario esté autenticado. No requiere un rol específico, lo que permite que la documentación sea accesible para cualquier usuario logueado.
2.  **Inclusión de Librerías**: Carga `src/lib/Parsedown.php` (la librería para parsear Markdown) y `src/config.php` (para la conexión a la base de datos, utilizada para obtener el nombre del período activo).
3.  **Recepción de Parámetro**: El script espera un parámetro `file` en la URL (`$_GET['file']`). Este parámetro contiene la ruta relativa al archivo Markdown que se desea visualizar (ej. `Módulo Estudiante/Funcionalidad_Modulo_Estudiantes.md`).
4.  **Medidas de Seguridad Críticas**: Antes de intentar leer cualquier archivo, el script implementa dos capas de seguridad esenciales para prevenir ataques de "Directory Traversal" (intentos de acceder a archivos fuera del directorio permitido):
    *   `$base_dir = realpath(__DIR__ . '/../Funcionality')`: Obtiene la ruta absoluta y real del directorio base donde se espera que estén los archivos de documentación.
    *   `$file_path = realpath($base_dir . '/' . $requested_file)`: Resuelve la ruta absoluta y real del archivo solicitado.
    *   `strpos($file_path, $base_dir) === 0`: Comprueba que la ruta real del archivo solicitado **comience exactamente** con la ruta real del directorio base. Esto asegura que el archivo solicitado esté contenido dentro del directorio `Funcionality` y no sea una ruta maliciosa que intente acceder a otros archivos del sistema.
5.  **Lectura y Conversión de Markdown**: Si el archivo es válido y existe:
    *   Lee el contenido del archivo Markdown (`file_get_contents()`).
    *   Instancia la clase `Parsedown`.
    *   Convierte el contenido Markdown a HTML utilizando `$Parsedown->text($markdown_content)`.
6.  **Extracción de Título Dinámico**: Utiliza una expresión regular (`preg_match('/^# (.+)/m', ...)` ) para buscar el primer encabezado de nivel 1 (`#`) en el contenido Markdown. El texto de este encabezado se utiliza como el título dinámico de la página (`$page_title`).
7.  **Manejo de Errores**: Si el parámetro `file` no se proporciona, el archivo no existe, no se puede leer o la validación de seguridad falla, se muestra un mensaje de error apropiado en la interfaz.

---

## 3. Estructura de la Interfaz (HTML)

La página `view_document.php` utiliza la plantilla estándar del sistema para mantener una apariencia consistente:

*   **Cabecera**: Incluye el logo del colegio, el título dinámico de la página (extraído del Markdown) y el nombre del período activo.
*   **Contenedor de Documentos (`.document-container`)**: Un `div` con estilos específicos para presentar el contenido HTML renderizado del Markdown. Estos estilos aseguran que el texto, los títulos, las listas, los bloques de código y otros elementos Markdown se vean de forma legible y agradable.
*   **Botón de Navegación**: Incluye un botón "Volver al Menú de Ayuda" que permite al usuario regresar fácilmente al menú principal del módulo.

---

## 4. Librerías Utilizadas

*   **Parsedown**: La librería PHP utilizada para la conversión eficiente y segura de Markdown a HTML.
