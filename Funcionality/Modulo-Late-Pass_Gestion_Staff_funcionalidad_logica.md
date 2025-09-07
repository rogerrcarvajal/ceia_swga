# Análisis de Funcionalidad: Módulo Late-Pass (Gestión de Staff)

Este documento describe el flujo de trabajo y los componentes técnicos para la consulta de registros de entrada y salida del personal (Staff).

### Componentes Principales

- **`pages/gestion_es_staff.php`**: Interfaz de usuario para la consulta de movimientos.
- **`public/js/gestion_es_staff.js`**: Lógica de cliente que maneja los filtros y la visualización de datos.
- **`api/consulta_movimientos_staff.php`**: Endpoint que provee los datos de asistencia desde el backend.
- **`src/reports_generators/pdf_movimiento_staff.php`**: Script para generar el reporte en formato PDF.

---

### 1. `pages/gestion_es_staff.php` (Interfaz de Consulta)

La página presenta una interfaz de consulta para que los administradores puedan supervisar la asistencia del personal.

- **Filtros:** La interfaz se basa en dos filtros principales:
    1.  **Filtro de Semana:** Un campo de tipo `week` que permite al usuario seleccionar un año y una semana específicos para la consulta.
    2.  **Filtro de Personal:** Un menú desplegable (`<select>`) que se puebla con la lista de todo el personal asignado al período escolar activo.
- **Visualización:** Una tabla HTML (`<tbody>`) sirve como contenedor para los resultados, que se cargan dinámicamente.
- **Exportación:** Un botón "Generar PDF" permite al usuario obtener una copia impresa de la consulta actual.

---

### 2. `public/js/gestion_es_staff.js` (Lógica de Cliente)

Este archivo controla la interactividad de la página sin necesidad de recargas.

#### Flujo de Trabajo

1.  **Inicialización:** Al cargar la página, el script establece la semana actual como valor por defecto en el filtro y ejecuta una primera consulta para mostrar los datos relevantes inmediatamente.
2.  **Interacción del Usuario:** El script escucha los eventos `change` en los dos filtros (semana y personal). Cada vez que el usuario modifica una selección, se invoca la función `consultarMovimientos()`.
3.  **Consulta de Datos (`consultarMovimientos`):**
    - Obtiene los valores de los filtros.
    - Realiza una petición `fetch` a la API `/api/consulta_movimientos_staff.php`, pasando los filtros como parámetros en la URL.
    - Procesa la respuesta JSON de la API y la utiliza para construir dinámicamente las filas (`<tr>`) de la tabla de resultados.
4.  **Generación de PDF (`generarPDF`):**
    - Al hacer clic en el botón de PDF, el script lee los valores actuales de los filtros.
    - Abre una nueva pestaña del navegador apuntando al script `pdf_movimiento_staff.php`, pasando los mismos filtros en la URL para asegurar que el contenido del PDF sea idéntico a la vista en pantalla.

---

### 3. `api/consulta_movimientos_staff.php` (API de Datos)

Este script PHP actúa como el proveedor de datos para el frontend.

#### Lógica de Backend

1.  Recibe los parámetros `semana` y `staff_id` desde la URL (`$_GET`).
2.  Convierte el formato de semana ISO (ej. `2025-W37`) en un rango de fechas de inicio y fin para poder consultar la base de datos.
3.  Ejecuta una consulta SQL a la tabla `entrada_salida_staff`, filtrando los registros por el `staff_id` y el rango de fechas calculado.
4.  Devuelve un arreglo de los registros encontrados en formato JSON.

### Conclusión y Puntos de Mejora

El módulo sigue un patrón de diseño consistente y moderno, separando claramente las responsabilidades del frontend y el backend.

- **Consistencia:** El flujo de trabajo es casi idéntico al de la gestión de Late-Pass de estudiantes, lo que facilita el mantenimiento y la comprensión del sistema.
- **Punto de Mejora:** Se ha identificado una pequeña inconsistencia. La interfaz de usuario presenta una opción para ver "Todo el Personal", pero la API (`consulta_movimientos_staff.php`) está diseñada para devolver resultados solo cuando se le proporciona un `staff_id` específico. Devolver un arreglo vacío en lugar de todos los registros podría ser confuso para el usuario. Se podría mejorar la API para que, si no recibe un `staff_id` o recibe "todos", devuelva los registros de todo el personal para esa semana.