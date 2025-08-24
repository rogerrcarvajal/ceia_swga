# Documentación del Archivo: `pages/menu_reportes.php`

## 1. Propósito del Archivo

Este archivo PHP actúa como el **menú principal y punto de acceso centralizado** para todas las funcionalidades de generación de reportes del sistema SWGA. Su objetivo es proporcionar a los usuarios (con roles `admin`, `master` o `consulta`) una interfaz clara para seleccionar y generar los diversos informes disponibles.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

La lógica del lado del servidor de esta página es mínima y se enfoca en la seguridad y el contexto:

1.  **Control de Acceso**: Verifica que el usuario esté autenticado y que su rol sea uno de los permitidos (`admin`, `master`, `consulta`).
2.  **Obtención del Período Activo**: Consulta la base de datos para obtener y mostrar el nombre del período escolar activo, proporcionando un contexto relevante para los reportes.

---

## 3. Estructura de la Interfaz y Opciones

La página presenta un menú de opciones claro y directo, utilizando el estilo `lista-menu` para consistencia visual. Ofrece las siguientes tres opciones principales de reporte:

1.  **Planilla de Inscripción**
    *   **Enlace**: `pages/seleccionar_planilla.php`
    *   **Descripción**: "Permite la selección de un estudiante para obtener la planilla de inscripción."
    *   **Funcionalidad**: Dirige a una página donde el usuario puede elegir un estudiante para generar su planilla de inscripción individual en PDF.

2.  **Roster Actualizado**
    *   **Enlace**: `src/reports_generators/roster_actual.php`
    *   **Descripción**: "Vista previa del personal administrativo y docente, además un listado de estudiantes por grado, con opciones para exportar a PDF."
    *   **Funcionalidad**: Genera un reporte consolidado que incluye información del personal y listados de estudiantes, con opciones de exportación.

3.  **Gestionar Reportes de Estudiantes/Staff**
    *   **Enlace**: `pages/gestionar_reportes.php`
    *   **Descripción**: "Genera reportes detallados en PDF para estudiantes, diferentes categorías de staff y vehículos autorizados."
    *   **Funcionalidad**: Conduce a una interfaz más avanzada para generar reportes específicos y detallados, probablemente con filtros adicionales.
