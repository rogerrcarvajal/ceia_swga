# Documentación del Archivo: `public/js/gestion_vehiculos.js`

## 1. Propósito del Archivo

Este archivo JavaScript es el controlador del lado del cliente para la página de "Gestión y consulta de Entrada/Salida Vehículos" (`pages/gestion_vehiculos.php`). Su función principal es permitir la interacción del usuario con los filtros, solicitar los datos de movimientos vehiculares a la API correspondiente y renderizar dinámicamente los resultados en una tabla HTML, además de gestionar la generación de reportes en PDF.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Inicialización (`DOMContentLoaded`)

Al cargar la página, el script realiza las siguientes acciones:

1.  **Establece la Semana Actual**: Calcula la semana ISO actual y la pre-selecciona en el campo de filtro de semana (`<input type="week">`).
2.  **Asigna Event Listeners**: 
    *   Asocia la función `consultarMovimientos` al evento `change` de los filtros de semana y vehículo. Esto asegura que la tabla se actualice automáticamente cada vez que el usuario cambie un filtro.
    *   Asocia la función anónima para generar el PDF al evento `click` del botón "Generar PDF".
3.  **Carga Inicial de Datos**: Llama a `consultarMovimientos()` una vez al inicio para poblar la tabla con los registros de la semana actual por defecto.

### b. Flujo de Consulta de Datos (`consultarMovimientos`)

Esta función asíncrona es el corazón de la interactividad:

1.  **Obtiene Filtros**: Lee los valores seleccionados en los filtros de semana y vehículo.
2.  **Construye la URL de la API**: Crea la URL para la API `api/consultar_movimiento_vehiculos.php`, incluyendo los parámetros `semana` y `vehiculo_id`.
3.  **Llamada a la API (Fetch)**: Realiza una petición `GET` a la URL construida para obtener los datos de movimientos vehiculares.
4.  **Renderizado de la Tabla**: 
    *   Limpia el contenido actual del cuerpo de la tabla (`<tbody>`).
    *   Si la API devuelve datos, itera sobre cada registro y crea una nueva fila (`<tr>`) en la tabla.
    *   Muestra los detalles del vehículo (placa, modelo, nombre del estudiante asociado), las horas de entrada/salida, quién lo registró y cualquier observación.
    *   Si no se encuentran registros o hay un error, muestra un mensaje informativo en la tabla.

### c. Flujo de Generación de PDF

1.  **Obtiene Filtros**: Lee los valores actuales de los filtros de semana y vehículo.
2.  **Construye la URL del Reporte**: Crea una URL que apunta al script generador de PDFs: `src/reports_generators/generar_movimiento_vehiculos_pdf.php`, pasando los filtros como parámetros.
3.  **Abre Nueva Pestaña**: Utiliza `window.open(url, "_blank")` para abrir el PDF generado en una nueva pestaña del navegador, permitiendo al usuario ver o descargar el reporte sin salir de la página de consulta.
