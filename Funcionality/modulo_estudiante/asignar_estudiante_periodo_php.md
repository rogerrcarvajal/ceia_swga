# Documentación del Archivo: `pages/asignar_estudiante_periodo.php`

## 1. Propósito del Archivo

Este archivo proporciona una interfaz de **gestión masiva** para matricular estudiantes en cualquier período escolar, ya sea activo o pasado. A diferencia de `gestionar_estudiantes.php` (que gestiona un solo estudiante a la vez), esta página permite a un administrador seleccionar un período escolar y ver de un vistazo qué estudiantes están asignados y cuáles no, facilitando la asignación de múltiples estudiantes de forma rápida y eficiente.

La página es una estructura HTML estática que cobra vida a través del archivo `public/js/admin_asignar_estudiante.js`, el cual se encarga de toda la lógica de carga de datos y de las asignaciones.

---

## 2. Lógica de Carga (PHP)

La lógica del lado del servidor en la carga inicial es mínima y se centra en preparar los componentes básicos del formulario:

1.  **Control de Acceso**: Valida la sesión y el rol del usuario (`master` o `admin`).
2.  **Obtención de Períodos**: Realiza una consulta a la base de datos para obtener la lista de **todos** los períodos escolares registrados. Esta lista se utiliza para poblar el menú desplegable principal (`<select id="periodo_selector">`), que es el punto de partida de la interacción del usuario.
3.  **Lista de Grados**: Define un array estático en PHP con todos los grados disponibles (desde Daycare hasta Grade 12) para rellenar el menú de selección de grado en el formulario de asignación.

---

## 3. Estructura de la Interfaz (HTML)

La interfaz se divide en un diseño de dos paneles:

### a. Panel Izquierdo (`left-panel`)

*   **Selector de Período**: El elemento más importante, un menú desplegable (`<select id="periodo_selector">`) que contiene todos los períodos escolares. La selección de un período en este menú es el evento que desencadena toda la funcionalidad de la página.
*   **Lista de Asignados**: Una lista vacía (`<ul id="lista_estudiantes_asignados">`) que será poblada dinámicamente por JavaScript con los nombres de los estudiantes que ya están asignados al período seleccionado.

### b. Panel Derecho (`right-panel`)

*   **Estado Inicial**: Por defecto, muestra un mensaje pidiendo al usuario que seleccione un período.
*   **Formulario de Asignación (`form_asignar_estudiante`)**: Este formulario está oculto (`display:none;`) hasta que se selecciona un período. Contiene:
    *   Un campo oculto (`periodo_id_hidden`) para almacenar el ID del período seleccionado.
    *   Un menú desplegable (`<select name="estudiante_id">`) que será poblado por JavaScript con la lista de estudiantes **no asignados** a ese período.
    *   Un menú desplegable con la lista de todos los grados disponibles.
    *   Un botón "Asignar Estudiante" para enviar el formulario.

---

## 4. Vínculo con el Frontend

La funcionalidad completa de esta página depende del siguiente archivo JavaScript, que se carga al final del `<body>`:

```html
<script src="/ceia_swga/public/js/admin_asignar_estudiante.js"></script>
```

Este script es responsable de:
*   Detectar el cambio en el selector de período.
*   Realizar llamadas a las APIs para obtener las listas de estudiantes asignados y no asignados.
*   Actualizar dinámicamente el contenido de los paneles izquierdo y derecho.
*   Manejar el envío del formulario de asignación a la API correspondiente.
