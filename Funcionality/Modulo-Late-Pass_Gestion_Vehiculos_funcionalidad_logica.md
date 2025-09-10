# Análisis de Funcionalidad: Módulo Late-Pass (Gestión de Vehículos)

Este documento describe el flujo de trabajo y los componentes técnicos para la consulta de registros de entrada y salida de los vehículos autorizados.

### Componentes Principales

- **`pages/gestion_vehiculos.php`**: Interfaz de usuario para la consulta de movimientos vehiculares.
- **`public/js/gestion_vehiculos.js`**: Lógica de cliente que maneja los filtros y la visualización de datos.
- **`api/consulta_movimientos_vehiculos.php`**: Endpoint que provee los datos de los movimientos desde el backend.
- **`src/reports_generators/generar_movimiento_vehiculos_pdf.php`**: Script para generar el reporte en formato PDF.

---

### 1. `pages/gestion_vehiculos.php` (Interfaz de Consulta)

La página presenta una interfaz de consulta para que los administradores puedan supervisar el historial de entradas y salidas de los vehículos vinculados a los estudiantes.

- **Filtros:** La interfaz se basa en dos filtros principales:
    1.  **Filtro de Semana:** Un campo de tipo `week` para seleccionar la semana de consulta.
    2.  **Filtro de Vehículo:** Un menú desplegable (`<select>`) poblado con todos los vehículos autorizados. Para facilitar la identificación, cada opción muestra la placa, el modelo y el nombre del estudiante asociado.
- **Visualización:** Una tabla HTML (`<tbody>`) sirve como contenedor para los resultados, que se cargan dinámicamente.
- **Exportación:** Un botón "Generar PDF" permite al usuario obtener una copia impresa de la consulta actual.

---

### 2. `public/js/gestion_vehiculos.js` (Lógica de Cliente)

Este archivo sigue el patrón de diseño consistente establecido en los otros módulos de gestión, controlando la interactividad de la página.

#### Flujo de Trabajo

1.  **Inicialización:** Al cargar la página, el script establece la semana actual como valor por defecto en el filtro y ejecuta una primera consulta para mostrar los datos relevantes de inmediato.
2.  **Interacción del Usuario:** El script escucha los eventos `change` en los dos filtros (semana y vehículo). Cada vez que el usuario modifica una selección, se invoca la función `consultarMovimientos()`.
3.  **Consulta de Datos (`consultarMovimientos`):**
    - Obtiene los valores de los filtros.
    - Realiza una petición `fetch` a la API `/api/consulta_movimientos_vehiculos.php`, pasando los filtros como parámetros en la URL.
    - Procesa la respuesta JSON de la API y la utiliza para construir dinámicamente las filas (`<tr>`) de la tabla de resultados.
4.  **Generación de PDF (`generarPDF`):**
    - Al hacer clic en el botón de PDF, el script lee los valores actuales de los filtros.
    - Abre una nueva pestaña del navegador apuntando al script `generar_movimiento_vehiculos_pdf.php`, pasando los mismos filtros en la URL para asegurar que el contenido del PDF sea idéntico a la vista en pantalla.

---

### 3. `api/consulta_movimientos_vehiculos.php` (API de Datos)

Este script PHP actúa como el proveedor de datos para el frontend.

#### Lógica de Backend

1.  Recibe los parámetros `semana` y `vehiculo_id` desde la URL (`$_GET`).
2.  Convierte el formato de semana ISO (ej. `2025-W37`) en un rango de fechas de inicio y fin.
3.  Ejecuta una consulta SQL a la tabla `registro_vehiculos`. Realiza `JOIN` con las tablas `vehiculos` y `estudiantes` para poder mostrar una descripción completa del vehículo y a quién pertenece.
4.  Filtra los registros por el `vehiculo_id` y el rango de fechas calculado.
5.  Devuelve un arreglo de los registros encontrados en formato JSON.

### Conclusión

El módulo de gestión de vehículos es el tercer y último pilar de las consultas del sistema Late-Pass. Su implementación es consistente con los módulos de Staff y Late-Pass de estudiantes, demostrando una arquitectura de software bien planificada y coherente.

Al igual que en el módulo de Staff, se observa que la API está preparada para consultas individuales por vehículo, mientras que la interfaz ofrece una opción "Todos" que actualmente no devuelve resultados. La habilitación de esta funcionalidad completaría la potencia del módulo de consulta.