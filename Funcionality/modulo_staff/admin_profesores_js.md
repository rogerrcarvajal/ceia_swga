# Funcionalidad del JavaScript `admin_profesores.js`

Este archivo JavaScript es responsable de toda la interactividad en la página de gestión de profesores (`gestionar_profesor.php`). Permite a los administradores ver, asignar y editar la información de los profesores para un período escolar específico.

## Lógica de Negocio

1.  **Carga Inicial y Filtro por Período:**
    *   Al cargar la página, el script automáticamente realiza una petición a `api/obtener_profesores.php`, enviando el ID del período escolar seleccionado por defecto.
    *   La respuesta (una lista de profesores) se usa para construir dinámicamente la tabla principal.
    *   Si el usuario cambia el período escolar en el menú desplegable, el script vuelve a lanzar la petición para recargar la tabla con los profesores correspondientes al nuevo período.

2.  **Asignación de Nuevos Profesores:**
    *   Un botón permite mostrar un formulario para asignar un profesor a un período.
    *   Al mostrar el formulario, se ejecutan dos peticiones asíncronas:
        1.  A `api/obtener_profesores_no_asignados.php` para poblar un menú desplegable con los profesores que aún no están en la lista de ese período.
        2.  Se carga dinámicamente otro menú con las opciones de "Homeroom" (guía de curso).
    *   Cuando el administrador envía el formulario, los datos se mandan a `api/asignar_profesor.php` mediante POST.
    *   Tras una respuesta exitosa, el formulario se oculta, se resetea y la tabla de profesores se recarga para reflejar la nueva asignación.

3.  **Edición en Línea (Inline Editing):**
    *   El script permite la edición directa de los campos "Posición" y "Homeroom Teacher" en la tabla.
    *   Al hacer clic en una de estas celdas, su contenido se reemplaza por un menú desplegable (`<select>`) con las opciones predefinidas.
    *   Cuando el usuario selecciona una opción y sale del campo (evento `blur`) o presiona "Enter", se invoca la función `guardarCambio`.
    *   Esta función envía los datos (el ID de la asignación, el campo modificado y el nuevo valor) al script `api/actualizar_profesores.php` para que persista el cambio en la base de datos.
    *   Si la actualización falla, la celda revierte a su valor original. El usuario puede cancelar la edición presionando la tecla "Escape".

4.  **Notificaciones al Usuario:**
    *   Todas las operaciones (carga, asignación, actualización) proporcionan retroalimentación visual al usuario a través de un área de mensajes (`status-message`).
    *   Los mensajes de éxito o error se muestran de forma clara y desaparecen automáticamente después de unos segundos.
