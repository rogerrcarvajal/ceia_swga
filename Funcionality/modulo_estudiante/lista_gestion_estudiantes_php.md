# Documentación del Archivo: `pages/lista_gestion_estudiantes.php`

## 1. Propósito del Archivo

Este archivo actúa como el **portal de entrada** para la funcionalidad de "Asignación a Períodos Escolares". Su único propósito es mostrar una lista completa de todos los estudiantes registrados en el sistema, permitiendo al administrador seleccionar a cuál de ellos desea gestionar su asignación a un período y grado.

Es una página de solo lectura que sirve para la navegación y selección.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

1.  **Control de Acceso**: El script verifica la sesión del usuario y se asegura de que su rol sea `master` o `admin` para poder acceder a la página.
2.  **Obtención de Datos**: Realiza una consulta a la base de datos para obtener el `id`, `nombre_completo` y `apellido_completo` de todos los estudiantes de la tabla `estudiantes`.
3.  **Ordenamiento**: La lista de estudiantes se ordena alfabéticamente por apellido y nombre para facilitar la búsqueda visual por parte del administrador.

---

## 3. Estructura de la Interfaz y Flujo de Navegación

*   **Interfaz Principal**: La página renderiza una lista vertical (`<ul>`). Cada elemento (`<li>`) de la lista contiene:
    *   El nombre completo del estudiante.
    *   Un botón/enlace con el texto "Gestionar".

*   **Flujo de Navegación**: El enlace "Gestionar" es la parte clave de la funcionalidad. Su atributo `href` está construido dinámicamente para apuntar a la siguiente página del flujo, `pages/gestionar_estudiantes.php`.

    **Ejemplo de enlace generado:**
    ```html
    <a href="/ceia_swga/pages/gestionar_estudiantes.php?id=V12345678" class="btn-gestionar">Gestionar</a>
    ```

    Al hacer clic en este enlace, el administrador es redirigido a la página de gestión específica para el estudiante seleccionado, pasando el `id` del estudiante como un parámetro en la URL. Este `id` es fundamental para que la siguiente página sepa qué estudiante cargar.
