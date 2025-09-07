# Análisis de Funcionalidad: Módulo de Reportes

Este documento describe el flujo de trabajo y los componentes técnicos del "Módulo de Reportes", cuya función principal es la extracción de datos y su presentación en formato PDF.

---

### Arquitectura General

A diferencia de otros módulos interactivos del sistema, el Módulo de Reportes sigue una arquitectura clásica basada en PHP. La lógica principal reside en el backend, que se encarga de consultar la base de datos y renderizar la información. El uso de JavaScript es mínimo y se limita a mejorar la usabilidad de la interfaz, sin realizar llamadas a APIs para la obtención de datos.

---

### 1. Generar Planilla de Inscripción (`seleccionar_planilla.php`)

-   **Funcionalidad:** Permite al administrador generar un PDF con la planilla de inscripción completa de un estudiante específico.
-   **Flujo de Trabajo:**
    1.  La página carga una lista desplegable con todos los estudiantes inscritos en el período escolar activo.
    2.  El administrador selecciona un estudiante.
    3.  Al hacer clic en "Generar PDF", el ID del estudiante seleccionado se envía mediante un formulario GET al script `src/reports_generators/generar_planilla_pdf.php`.
-   **Lógica Técnica:** El script generador de PDF (inferido) recibe el ID, consulta toda la información asociada al estudiante (datos personales, padres, ficha médica) y la maqueta en un documento PDF detallado.

---

### 2. Roster Actualizado (`roster_actual.php`)

-   **Funcionalidad:** Ofrece una vista consolidada de todo el personal y los estudiantes activos en el período actual, con la opción de exportar esta vista a PDF.
-   **Flujo de Trabajo:**
    1.  Al cargar la página, el backend consulta y muestra en dos tablas separadas las listas de "Staff" y "Estudiantes" activos.
    2.  Un botón "Generar PDF del Roster" envía un formulario (sin necesidad de parámetros adicionales) al script `src/reports_generators/generar_roster_pdf.php`.
-   **Lógica Técnica:** El script generador de PDF replica la misma consulta de la página principal para obtener las listas de personal y estudiantes activos y las formatea en un documento PDF.

---

### 3. Gestionar Reportes (`gestionar_reportes.php`)

-   **Funcionalidad:** Sirve como un panel centralizado para previsualizar y generar múltiples reportes de listas categorizadas.
-   **Flujo de Trabajo:**
    1.  **Carga Inicial:** Al cargar la página, el backend ejecuta todas las consultas necesarias para cada categoría de reporte (Estudiantes, Staff Administrativo, Staff Docente, etc.) y las renderiza en tablas HTML que permanecen ocultas inicialmente.
    2.  **Interacción del Usuario:** El usuario hace clic en una categoría en el menú lateral (ej. "Vehículos Autorizados").
    3.  **Visualización:** Un script de JavaScript simple se activa para mostrar la sección de vista previa correspondiente a la categoría seleccionada.
    4.  **Generación de PDF:** Cada sección de vista previa tiene su propio botón "Generar PDF", que apunta a un script generador de PDF específico para esa categoría (ej. `generar_lista_vehiculos_autorizados_PDF.php`).
-   **Lógica Técnica:** Este enfoque precarga todos los datos, haciendo que la experiencia de usuario para cambiar entre vistas previas sea instantánea. La modularidad es alta, ya que cada reporte tiene su propio script generador de PDF, facilitando el mantenimiento.

---

### Conclusión General del Módulo

El "Módulo de Reportes" es robusto y cumple su propósito de manera efectiva y directa. Su arquitectura basada en PHP es adecuada para la tarea de generar vistas de datos estáticas.

-   **Fortalezas:**
    -   **Claridad y Sencillez:** La lógica es directa y fácil de mantener.
    -   **Modularidad:** El uso de un script generador de PDF dedicado para cada reporte es una excelente práctica de diseño que aísla la lógica de cada uno.
    -   **Eficiencia:** En `gestionar_reportes.php`, la precarga de datos permite una navegación fluida entre las diferentes vistas previas sin esperas adicionales.