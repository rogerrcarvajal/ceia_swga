# Documentación del Archivo: `public/js/gestion_es_staff.js`

## 1. Propósito del Archivo

Este archivo JavaScript es el motor interactivo de la página de consulta de asistencia del personal (`pages/gestion_es_staff.php`). Su responsabilidad es detectar las acciones del usuario (cambiar filtros, hacer clic en botones), solicitar los datos de asistencia correspondientes a una API y renderizar dinámicamente los resultados en la tabla de la página.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Inicialización (`DOMContentLoaded`)

Al cargarse la página, el script realiza varias acciones preparatorias:

1.  **Establece la Semana Actual**: Calcula cuál es la semana en curso y la establece como valor por defecto en el selector de semana (`<input type="week">`).
2.  **Asigna Event Listeners**: 
    *   Asocia la función `consultarMovimientos` al evento `change` de los dos filtros (semana y personal). Esto significa que cada vez que el usuario cambie uno de los filtros, se lanzará una nueva consulta.
    *   Asocia la función `generarPDF` al evento `click` del botón "Generar PDF".
3.  **Carga Inicial de Datos**: Llama a `consultarMovimientos()` una vez al inicio para que la tabla muestre los datos de la semana actual para "Todo el Personal" sin que el usuario tenga que hacer nada.

### b. Flujo de Consulta de Movimientos (`consultarMovimientos`)

Esta función asíncrona es el núcleo del script:

1.  **Obtiene Filtros**: Lee los valores actuales del selector de semana y de personal.
2.  **Construye la URL**: Crea dinámicamente la URL para la API `api/consultar_movimiento_staff.php`. La URL base siempre incluye el parámetro `semana`. Si se ha seleccionado un miembro del staff específico (no la opción "todos"), añade el `staff_id` a la URL.
3.  **Llamada a la API (Fetch)**: Realiza una petición `GET` a la URL construida.
4.  **Renderizado de la Tabla**: 
    *   Limpia el cuerpo de la tabla (`<tbody>`).
    *   Analiza la respuesta JSON de la API.
    *   Si la respuesta es exitosa y contiene datos, itera sobre el array de registros y crea una nueva fila (`<tr>`) por cada uno, rellenando las celdas (`<td>`) con el nombre, fecha, hora de entrada/salida y estado de ausencia.
    *   Si no hay datos o la API devuelve un error, muestra un mensaje informativo en una única fila de la tabla.

### c. Flujo de Generación de PDF (`generarPDF`)

1.  **Obtiene Filtros**: Lee los valores actuales de los filtros de semana y personal.
2.  **Construye la URL**: Crea una URL apuntando al script generador de PDFs: `src/reports_generators/generar_movimiento_staff_pdf.php`.
3.  **Pasa Parámetros**: Añade los valores de los filtros como parámetros a la URL del script de PDF.
4.  **Abre Nueva Pestaña**: Utiliza `window.open(url, "_blank")` para abrir el PDF generado en una nueva pestaña del navegador, sin que el usuario pierda la vista de la página de consulta.
