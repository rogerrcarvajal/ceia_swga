# Documentación del Archivo: `pages/menu_estudiantes.php`

## 1. Propósito del Archivo

Este archivo PHP actúa como el **menú principal y panel de navegación** para todas las funcionalidades relacionadas con la gestión de estudiantes. Su propósito es ofrecer al administrador un acceso directo y claro a las diferentes tareas que puede realizar dentro de este módulo.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

La lógica del lado del servidor de esta página es muy sencilla:

1.  **Control de Acceso**: Verifica que el usuario haya iniciado sesión y que su rol sea `master` o `admin`.
2.  **Obtención del Período Activo**: Realiza una consulta a la base de datos para obtener y mostrar el nombre del período escolar que se encuentra activo, proporcionando contexto al usuario.

---

## 3. Estructura de la Interfaz y Opciones

La página utiliza el mismo estilo visual que otros menús del sistema (`lista-menu`) para presentar una apariencia consistente. Ofrece las siguientes cuatro opciones:

1.  **Planilla de Inscripción**
    *   **Descripción**: "Permite el ingreso de un nuevo estudiante a través de la Planilla de Inscripción".
    *   **Enlace**: Apunta a `pages/planilla_inscripcion.php`.
    *   **Funcionalidad**: Inicia el flujo para registrar un estudiante desde cero.

2.  **Gestionar Planilla de Inscripción**
    *   **Descripción**: "Permite a través de una consulta dinámica, editar la Planilla de Inscripción".
    *   **Enlace**: Apunta a `pages/administrar_planilla_estudiantes.php`.
    *   **Funcionalidad**: Lleva al potente editor de expedientes que ya hemos documentado.

3.  **Gestionar Estudiantes**
    *   **Descripción**: "Permite gestionar y asignar/vincular a un estudiante con el Período Escolar activo".
    *   **Enlace**: Apunta a `pages/asignar_estudiante_periodo.php`.
    *   **Funcionalidad**: Inicia el flujo para la matriculación de estudiantes en el período actual.

4.  **Gestionar Vehículos Autorizados**
    *   **Descripción**: "Permite gestionar y asignar/vincular vehículos a los estudiantes".
    *   **Enlace**: Apunta a `pages/registro_vehiculos.php`.
    *   **Funcionalidad**: Abre la gestión de vehículos, una sub-sección importante dentro del módulo de estudiantes.
