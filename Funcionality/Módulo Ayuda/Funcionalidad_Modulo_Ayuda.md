# Documentación del Módulo de Ayuda

## 1. Propósito del Módulo

El Módulo de Ayuda, accesible a través de la página `pages/menu_ayuda.php`, ha sido reestructurado para convertirse en el centro de conocimiento centralizado del sistema SWGA. Su objetivo es ofrecer dos niveles de soporte distintos pero complementarios:

1.  **Soporte al Usuario Final**: A través de un manual de usuario claro y conciso que explica cómo realizar las operaciones del día a día.
2.  **Soporte Técnico y Académico**: Mediante una base de conocimiento detallada que documenta la lógica de negocio, la arquitectura y el funcionamiento de cada componente del sistema, ideal para la formación de nuevos administradores y como material de referencia para la tesis de grado.

## 2. Flujo de Trabajo y Componentes

*   **Página Principal**: `pages/menu_ayuda.php`
*   **Visor de Documentos**: `pages/view_document.php`
*   **Librería de Soporte**: `src/lib/Parsedown.php`

El flujo de este módulo es el siguiente:

1.  **Acceso al Menú**: El usuario accede a `menu_ayuda.php` desde la barra de navegación principal.
2.  **Selección de Opción**: La página presenta un menú interactivo con dos opciones principales.
3.  **Visualización de Documentación**: Al hacer clic en cualquier enlace del menú, el usuario es dirigido a la página `view_document.php`. Se pasa como parámetro en la URL (`?file=...`) la ruta al archivo Markdown (`.md`) que se debe mostrar.
4.  **Renderizado y Presentación**: El visor de documentos utiliza la librería `Parsedown` para convertir el contenido del archivo Markdown a HTML sobre la marcha y lo presenta dentro de la plantilla visual del sistema, asegurando una experiencia de usuario consistente.

## 3. Estructura del Menú de Ayuda

El menú se divide en las siguientes secciones:

### a. Manual de Usuario

*   **Enlace**: Apunta a la visualización del archivo `Funcionalidad_Modulo_Ayuda_Manua_Usuario.md`.
*   **Contenido**: Contiene la guía de uso estándar del sistema, explicando las tareas más comunes desde la perspectiva de un usuario administrador (cómo inscribir, cómo generar un pase, etc.).

### b. Documentación y Funcionalidad del Sistema

*   **Interfaz**: Es un submenú desplegable para mantener la interfaz limpia y organizada.
*   **Propósito**: Ofrece acceso a la documentación técnica de alto nivel para cada módulo del sistema.
*   **Contenido**: Cada enlace de este submenú apunta a un archivo `.md` específico que detalla el propósito, los componentes (archivos involucrados) y el flujo de operación de dicho módulo. Los documentos disponibles son:
    *   Módulo Estudiante
    *   Módulo Staff
    *   Módulo Late-Pass
    *   Módulo Reportes
    *   Módulo Mantenimiento
    *   Módulo Ayuda (este mismo documento)

Esta estructura dual permite que tanto los usuarios operativos como los técnicos o académicos encuentren la información que necesitan de forma rápida y eficiente.
