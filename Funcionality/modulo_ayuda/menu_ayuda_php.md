# Documentación del Archivo: `pages/menu_ayuda.php`

## 1. Propósito del Archivo

`menu_ayuda.php` es la página principal y el punto de acceso al **Módulo de Ayuda**. Su función es presentar al usuario una interfaz clara y organizada con todas las opciones de documentación disponibles en el sistema. Actúa como un índice o portal desde el cual los usuarios pueden navegar para consultar tanto guías de uso general como documentación técnica detallada.

---

## 2. Lógica de Negocio y Flujo de Operación

El script sigue una lógica sencilla pero estructurada para construir la página del menú.

### a. Seguridad y Sesión

1.  **Verificación de Autenticación**: Al inicio del script, se comprueba si existe una sesión de usuario activa (`$_SESSION['usuario']`). Si el usuario no está autenticado, es redirigido inmediatamente a la página de inicio de sesión (`/ceia_swga/public/index.php`). Esto asegura que solo el personal autorizado pueda acceder a la documentación del sistema.

### b. Carga de Datos y Configuración

1.  **Inclusión de Archivos**: Se incluyen dos archivos esenciales:
    *   `src/config.php`: Para establecer la conexión con la base de datos.
    *   `src/templates/navbar.php`: Para renderizar la barra de navegación estándar del sistema, manteniendo una experiencia de usuario consistente.
2.  **Obtención del Período Activo**: Se realiza una consulta a la base de datos para obtener el nombre del período escolar que está actualmente activo. Aunque esta información no es crítica para la funcionalidad del menú, se muestra en la cabecera de la página para proporcionar contexto al usuario.

### c. Estructura de la Interfaz (HTML y CSS)

1.  **Contenido Principal**: La página muestra el logo del CEIA, un título principal "Gestión de Ayuda y Soporte" y el período escolar activo.
2.  **Menú de Opciones**: El núcleo de la página es una lista no ordenada (`<ul>`) que presenta las diferentes categorías de documentación. Cada elemento de la lista está diseñado para ser claro y descriptivo:
    *   **Manual de Usuario**: Un enlace directo al `view_document.php` que carga la guía completa del sistema. Incluye un título y una breve descripción de su contenido.
    *   **Documentación y Funcionalidad del Sistema**: Este es un elemento de menú desplegable (submenu) que contiene enlaces a la documentación técnica de cada módulo principal del sistema (Estudiante, Staff, Late-Pass, etc.).
3.  **Interactividad (JavaScript)**:
    *   Se utiliza un pequeño script de JavaScript (`toggleSubmenu`) para controlar el comportamiento del menú desplegable. Al hacer clic en "Documentación y Funcionalidad del Sistema", el submenu correspondiente se muestra u oculta, permitiendo una navegación más limpia y organizada sin recargar la página.
4.  **Navegación**: Se incluye un botón "Volver" que permite al usuario regresar fácilmente al dashboard principal (`pages/dashboard.php`).

### d. Enlaces al Visor de Documentos

Todos los enlaces de la documentación apuntan a `pages/view_document.php`. La diferencia entre cada enlace es el valor del parámetro `file` en la URL. Este parámetro le indica al visor qué archivo `.md` específico debe cargar y renderizar desde el directorio `Funcionality/`.

**Ejemplo de enlace:**
`href="/ceia_swga/pages/view_document.php?file=Módulo Estudiante/Funcionalidad_Modulo_Estudiantes.md"`

Este enlace le pide a `view_document.php` que muestre el archivo `Funcionalidad_Modulo_Estudiantes.md` que se encuentra en la subcarpeta `Módulo Estudiante/`.