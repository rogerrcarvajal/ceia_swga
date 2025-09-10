# Documentación del Archivo: `public/js/admin_asignar_estudiante.js`

## 1. Propósito del Archivo

Este archivo JavaScript es el responsable de toda la lógica y la interactividad de la página de asignación masiva de estudiantes (`pages/asignar_estudiante_periodo.php`). Su función es comunicarse con el servidor para obtener listas de estudiantes y procesar las nuevas asignaciones, actualizando la interfaz en tiempo real sin recargar la página.

---

## 2. Lógica de Negocio y Flujo de Operación

El script se inicializa cuando el DOM está completamente cargado (`DOMContentLoaded`).

### a. Evento Principal: Selección de un Período Escolar

*   **Disparador (Trigger)**: El script escucha el evento `change` en el menú desplegable `<select id="periodo_selector">`.
*   **Acción**: Cuando el administrador selecciona un período:
    1.  Se obtiene el `periodoId` del período seleccionado.
    2.  Si se seleccionó un período válido, se oculta el panel informativo y se muestra el panel de asignación.
    3.  Se guarda el `periodoId` en un campo oculto del formulario para que sea enviado junto con la asignación.
    4.  Se ejecutan simultáneamente dos funciones asíncronas para cargar los datos: `cargarEstudiantesAsignados(periodoId)` y `cargarEstudiantesNoAsignados(periodoId)`.

### b. Evento de Asignación: Envío del Formulario

*   **Disparador (Trigger)**: El script escucha el evento `submit` en el formulario `<form id="form_asignar_estudiante">`.
*   **Acción**:
    1.  Se previene el envío tradicional del formulario con `e.preventDefault()`.
    2.  Se recogen los datos del formulario (ID del estudiante, ID del período y grado a cursar).
    3.  Se realiza una llamada `fetch` con el método `POST` a la API `api/asignar_estudiante.php`.
    4.  **Actualización en Tiempo Real**: Si la API devuelve una respuesta de éxito, el script **vuelve a llamar** a las funciones `cargarEstudiantesAsignados` y `cargarEstudiantesNoAsignados`. Esto refresca ambas listas automáticamente, moviendo al estudiante recién asignado de la lista de "no asignados" a la de "asignados", proporcionando un feedback visual inmediato y claro al usuario.
    5.  Se muestra un mensaje de éxito o error utilizando la función `mostrarMensaje`.

---

## 3. Funciones Asíncronas (AJAX)

*   **`cargarEstudiantesAsignados(periodoId)`**: 
    *   **API Invocada**: `api/obtener_estudiantes_por_periodo.php`.
    *   **Lógica**: Realiza una petición `GET` a la API para obtener la lista de estudiantes ya asignados a ese período. Limpia la lista `<ul>` del panel izquierdo y la repuebla dinámicamente, creando un elemento `<li>` por cada estudiante encontrado.

*   **`cargarEstudiantesNoAsignados(periodoId)`**: 
    *   **API Invocada**: `api/obtener_estudiantes_no_asignados.php`.
    *   **Lógica**: Realiza una petición `GET` a la API para obtener la lista de estudiantes que **no** están asignados a ese período. Limpia el menú desplegable `<select>` del formulario y lo repuebla con elementos `<option>`, uno por cada estudiante disponible para ser asignado.

---

## 4. Función Auxiliar

*   **`mostrarMensaje(status, message)`**: Muestra una notificación de éxito o error en un `div` específico (`#mensaje_asignacion`) y la oculta automáticamente después de 4 segundos.
