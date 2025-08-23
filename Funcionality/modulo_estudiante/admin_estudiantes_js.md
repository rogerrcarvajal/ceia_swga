# Documentación del Archivo: `public/js/admin_estudiantes.js`

## 1. Propósito del Archivo

Este archivo JavaScript es el motor que impulsa toda la interactividad de la página de gestión de expedientes (`pages/administrar_planilla_estudiantes.php`). Su responsabilidad es transformar una página estática en una aplicación web dinámica (Single Page Application - SPA) para la administración de datos, manejando eventos del usuario, comunicándose con el servidor de forma asíncrona (AJAX) y actualizando la interfaz sin necesidad de recargar la página.

---

## 2. Lógica de Negocio y Flujo de Operación

El script comienza su ejecución cuando el DOM (Modelo de Objetos del Documento) de la página está completamente cargado, gracias al event listener `DOMContentLoaded`.

### a. Inicialización y Event Listeners

Al iniciarse, el script asigna escuchadores de eventos a los elementos clave de la página:

1.  **Filtro de Búsqueda (`filtro_estudiantes`)**: Escucha el evento `keyup`. Con cada tecla que el usuario presiona, el script compara el texto introducido (en minúsculas) con el texto de cada elemento de la lista de estudiantes. Si un estudiante no coincide, se oculta (`display: 'none'`); si coincide, se muestra. Esto permite un filtrado en tiempo real.

2.  **Lista de Estudiantes (`lista_estudiantes`)**: Escucha el evento `click`. Si el clic ocurre sobre un elemento `<li>` de la lista, el script:
    *   Obtiene el `id` del estudiante desde el atributo `data-id`.
    *   Oculta el panel informativo inicial.
    *   Muestra el panel de datos que contiene los formularios.
    *   Llama a la función `cargarExpedienteCompleto(estudianteId)` para iniciar la carga de datos.

3.  **Formularios de Actualización**: Cada uno de los cuatro formularios (`#form_estudiante`, `#form_padre`, etc.) escucha el evento `submit`. En lugar de enviarse de la forma tradicional (lo que recargaría la página), el evento es interceptado y manejado por la función genérica `handleFormSubmit`, pasándole la URL de la API de actualización correspondiente.

### b. Carga de Datos del Expediente (`cargarExpedienteCompleto`)

Esta es la función central para la visualización de datos. Es una función `async` que realiza una secuencia de operaciones:

1.  **Reseteo**: Antes de cargar nuevos datos, resetea los cuatro formularios para limpiar cualquier información de un estudiante previamente seleccionado.
2.  **Llamadas a APIs (Fetch)**: Realiza hasta cuatro llamadas `fetch` en secuencia para obtener toda la información relacionada con el ID del estudiante seleccionado:
    *   Primero, obtiene los datos del estudiante desde `api/obtener_estudiante.php`.
    *   Si la respuesta incluye un `padre_id`, realiza una segunda llamada para obtener los datos del padre desde `api/obtener_padre.php`.
    *   Si la respuesta incluye un `madre_id`, realiza una tercera llamada para los datos de la madre desde `api/obtener_madre.php`.
    *   Finalmente, obtiene los datos de la ficha médica desde `api/obtener_ficha_medica.php`.
3.  **Población de Formularios**: Después de cada llamada exitosa a una API, invoca a la función `rellenarFormulario` para tomar los datos recibidos (en formato JSON) y colocarlos en los campos correspondientes del formulario adecuado.
4.  **Manejo de Errores**: Si alguna de las llamadas a la API falla o devuelve un error, la ejecución se detiene y se muestra un mensaje de error en la consola y en la interfaz de usuario.

### c. Actualización de Datos (`handleFormSubmit`)

Esta función `async` maneja el guardado de los cambios para los cuatro formularios de una manera genérica y reutilizable:

1.  **Previene el Envío Tradicional**: Llama a `event.preventDefault()` para evitar que el navegador recargue la página.
2.  **Recopilación de Datos**: Crea un objeto `FormData` a partir del formulario que disparó el evento. Esto recopila eficientemente todos los valores de los campos del formulario.
3.  **Llamada a la API (Fetch)**: Envía los datos recopilados a la URL de la API especificada (ej. `api/actualizar_estudiante.php`) usando el método `POST`.
4.  **Feedback al Usuario**: Una vez que la API responde, la función `mostrarMensaje` es llamada para informar al usuario si la actualización fue exitosa o si ocurrió un error.

### d. Funciones Auxiliares

*   **`rellenarFormulario(form, data)`**: Una función de ayuda que mapea inteligentemente los datos de un objeto JSON a los campos de un formulario HTML. Itera sobre las claves del objeto de datos y busca un campo en el formulario con un `name` que coincida. Es capaz de manejar tanto campos de texto normales como checkboxes.
*   **`mostrarMensaje(status, message)`**: Controla un `div` en la parte superior del panel de datos para mostrar mensajes de estado (éxito o error). El mensaje aparece, se le asigna una clase CSS para darle color (verde para éxito, rojo para error) y desaparece automáticamente después de 4 segundos.
