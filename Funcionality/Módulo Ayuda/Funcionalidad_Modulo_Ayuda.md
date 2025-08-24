# Documentación General del Módulo de Ayuda

## 1. Propósito del Módulo

El **Módulo de Ayuda** es un componente central del sistema SWGA, diseñado para proporcionar soporte y documentación tanto a usuarios finales como a desarrolladores. Su objetivo es centralizar el acceso a guías de usuario, manuales técnicos y explicaciones detalladas sobre la funcionalidad y la lógica de negocio de cada módulo del sistema.

Este módulo actúa como un portal de conocimiento, mejorando la experiencia de usuario al ofrecer respuestas claras a posibles dudas y facilitando el mantenimiento y la futura expansión del sistema al tener la documentación técnica fácilmente accesible.

---

## 2. Componentes Principales

El módulo se compone de varios archivos que trabajan en conjunto para ofrecer una experiencia fluida:

1.  **`pages/menu_ayuda.php`**: Es la página principal y el punto de entrada al módulo. Presenta al usuario un menú organizado desde donde puede navegar hacia el manual de usuario o la documentación técnica de los diferentes módulos del sistema.

2.  **`pages/view_document.php`**: Un visor dinámico y seguro encargado de leer archivos de documentación en formato Markdown (`.md`), convertirlos a HTML y presentarlos en pantalla de una manera legible y estilizada. Es el motor que permite renderizar todo el contenido del módulo.

3.  **`src/lib/Parsedown.php`**: Una librería de PHP externa y robusta que se especializa en la conversión de sintaxis Markdown a HTML. Es la dependencia clave que `view_document.php` utiliza para interpretar los archivos `.md`.

4.  **Directorio `Funcionality/`**: Este directorio es el repositorio central de toda la documentación en formato Markdown. Está estructurado en subcarpetas, una por cada módulo del sistema (`Módulo Estudiante`, `Módulo Staff`, etc.), conteniendo los archivos `.md` que explican en detalle cada proceso y archivo del sistema.

---

## 3. Flujo de Operación

El flujo de interacción del usuario con el Módulo de Ayuda es el siguiente:

1.  **Acceso**: El usuario accede al módulo a través del enlace en la barra de navegación principal, que lo dirige a `pages/menu_ayuda.php`.
2.  **Selección**: En el menú de ayuda, el usuario elige qué documento desea consultar. Las opciones incluyen el "Manual de Usuario" o la documentación técnica de un módulo específico (ej. "Módulo Estudiante").
3.  **Visualización**: Al hacer clic en un enlace, el navegador realiza una petición a `pages/view_document.php`, pasando la ruta del archivo `.md` a visualizar como un parámetro en la URL (ej. `?file=Módulo Estudiante/Funcionalidad_Modulo_Estudiantes.md`).
4.  **Procesamiento**:
    *   `view_document.php` recibe la petición. Por seguridad, verifica que el archivo solicitado se encuentre dentro del directorio `Funcionality/`.
    *   Lee el contenido del archivo Markdown especificado.
    *   Utiliza la librería `Parsedown.php` para transformar el contenido Markdown en código HTML.
    *   Inserta el HTML resultante en una plantilla de página web, que incluye la barra de navegación, estilos CSS y un botón para volver al menú principal.
5.  **Renderizado**: El servidor devuelve la página HTML completa al navegador del usuario, que la muestra de forma estilizada y fácil de leer.