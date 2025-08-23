# Documentación del Archivo: `pages/menu_ayuda.php`

## 1. Propósito del Archivo

Este archivo PHP sirve como el **menú principal y punto de acceso centralizado** para todas las funcionalidades de ayuda y documentación del sistema SWGA. Su objetivo es proporcionar a los usuarios una interfaz clara para acceder tanto al manual de usuario como a la documentación técnica detallada de cada módulo.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

La lógica del lado del servidor de esta página es mínima y se enfoca en la seguridad y el contexto:

1.  **Control de Acceso**: Verifica que el usuario esté autenticado. A diferencia de otros módulos, no requiere un rol específico (`master`, `admin`, `consulta`), lo que sugiere que la ayuda está disponible para cualquier usuario logueado.
2.  **Obtención del Período Activo**: Consulta la base de datos para obtener y mostrar el nombre del período escolar activo, proporcionando un contexto relevante en la interfaz.

---

## 3. Estructura de la Interfaz y Opciones

La página presenta un menú de opciones claro y directo, utilizando el estilo `lista-menu` para consistencia visual. Ofrece dos opciones principales:

1.  **Manual de Usuario**
    *   **Enlace**: `pages/view_document.php?file=Módulo Ayuda/Funcionalidad_Modulo_Ayuda_Manua_Usuario.md`
    *   **Descripción**: "Guía completa sobre el uso y las funcionalidades del sistema."
    *   **Funcionalidad**: Al hacer clic, redirige al visor de documentos para mostrar el manual de usuario general del sistema.

2.  **Documentación y Funcionalidad del Sistema**
    *   **Tipo**: Es un elemento de menú que contiene un **submenú desplegable**.
    *   **Descripción**: "Explicación técnica detallada de la lógica de negocio de cada módulo y sus componentes."
    *   **Funcionalidad**: Al hacer clic en el título, el submenú se expande/contrae, revelando enlaces a la documentación granular de cada módulo:
        *   Módulo Estudiante
        *   Módulo Staff
        *   Módulo Late-Pass
        *   Módulo Reportes
        *   Módulo Mantenimiento
        *   Módulo Ayuda (este mismo documento)
    *   **Enlaces del Submenú**: Cada enlace apunta a `pages/view_document.php`, pasando como parámetro la ruta relativa al archivo Markdown (`.md`) correspondiente dentro de la carpeta `Funcionality`.

### JavaScript Embebido

Un pequeño script JavaScript (`toggleSubmenu`) se encarga de la funcionalidad de expandir y contraer el submenú de "Documentación y Funcionalidad del Sistema", mejorando la usabilidad de la interfaz.
