# Documentación del Archivo: `pages/generar_qr.php`

## 1. Propósito del Archivo

Este archivo PHP proporciona una interfaz dinámica y centralizada para la **generación de códigos QR** en formato PDF para diferentes tipos de entidades: estudiantes, miembros del staff (clasificados por categoría) y vehículos. Permite al administrador seleccionar una categoría y luego un ítem específico dentro de esa categoría para generar su código QR individualizado.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Lógica de Carga (PHP)

La parte PHP de este archivo se encarga de preparar los datos necesarios para que el JavaScript pueda operar dinámicamente:

1.  **Control de Acceso**: Verifica la sesión del usuario y sus permisos (`master` o `admin`).
2.  **Obtención de Datos**: Realiza múltiples consultas a la base de datos para obtener listas de:
    *   Estudiantes (asignados al período activo).
    *   Vehículos (autorizados, con el nombre del estudiante asociado).
    *   Miembros del staff, clasificados por sus categorías (Administrativo, Docente, Mantenimiento, Vigilancia).
3.  **Transferencia de Datos a JavaScript**: Todos estos datos se codifican a formato JSON (`json_encode()`) y se insertan directamente en un bloque `<script>` en el HTML. Esto permite que el JavaScript acceda a toda la información necesaria sin tener que realizar llamadas AJAX adicionales en la carga inicial.

### b. Lógica del Frontend (JavaScript Embebido)

El script JavaScript embebido es el corazón de la interactividad de esta página:

1.  **Manejo del Menú Lateral**: Escucha los eventos `click` en los elementos de la lista del menú lateral (`.menu-lateral li`).
2.  **Activación Dinámica del Formulario**: Cuando se selecciona una categoría en el menú:
    *   Se resalta visualmente el ítem seleccionado.
    *   Se oculta el panel informativo inicial y se muestra el contenedor del formulario (`#form-container`).
    *   **Poblado del Selector**: El script toma la lista de ítems correspondiente a la categoría seleccionada (ej. `data.estudiantes` si se seleccionó "Estudiantes") y la utiliza para poblar dinámicamente el menú desplegable `<select id="select-item">`.
        *   Las opciones se formatean de manera diferente según la categoría (ej. "Apellido, Nombre" para estudiantes, "Placa - Modelo (Estudiante)" para vehículos).
    *   **Configuración Dinámica de la Acción del Formulario**: Esta es la funcionalidad clave. El script **modifica el atributo `action` del formulario (`qr-form`)** para que apunte al script generador de PDF correcto para la categoría seleccionada (ej. `generar_qr_pdf.php` para estudiantes, `generar_qr_staff_pdf.php` para staff, `generar_qr_vehiculo_pdf.php` para vehículos).
    *   **Actualización del Título**: El título del formulario (`#form-title`) también se actualiza para reflejar la categoría seleccionada.
3.  **Generación del PDF**: Cuando el usuario selecciona un ítem y hace clic en "Generar PDF con QR", el formulario se envía (usando el método `GET` y el `action` dinámicamente establecido) y el atributo `target="_blank"` asegura que el PDF se abra en una nueva pestaña del navegador.
