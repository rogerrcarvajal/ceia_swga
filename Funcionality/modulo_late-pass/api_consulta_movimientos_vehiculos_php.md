# Documentación del Archivo: `api/consulta_movimientos_vehiculos.php`

## 1. Propósito del Archivo

Este endpoint de API es el encargado de proporcionar los datos para la página de "Gestión y consulta de Entrada/Salida Vehículos" (`pages/gestion_vehiculos.php`). Su función es consultar la base de datos para obtener un listado detallado de los movimientos de vehículos (entradas y salidas), permitiendo filtrar por semana y por un vehículo específico.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetros**: El script obtiene los parámetros `semana` (en formato `YYYY-W##`) y `vehiculo_id` (el ID del vehículo o 'todos') de la URL (`GET`).

2.  **Validación de Parámetros**: Realiza una validación inicial. Si `semana` o `vehiculo_id` están vacíos, o si `vehiculo_id` es 'todos', devuelve una respuesta de éxito con un array de registros vacío. Esto significa que la API no devolverá datos si no se selecciona un vehículo específico.

3.  **Conversión de Semana a Rango de Fechas**: El script parsea el formato `YYYY-W##` de la semana para calcular la fecha de inicio (lunes) y la fecha de fin (domingo) de esa semana. Esto es crucial para filtrar los registros por fecha en la base de datos.

4.  **Consulta SQL**: Ejecuta una consulta SQL que une (`JOIN`) las tablas:
    *   **`registro_vehiculos`**: La tabla principal que almacena cada movimiento de entrada/salida.
    *   **`vehiculos`**: Para obtener la placa y el modelo del vehículo.
    *   **`estudiantes`**: Para obtener el nombre y apellido del estudiante asociado al vehículo.

5.  **Filtrado**: La cláusula `WHERE` filtra los registros por el `vehiculo_id` y por el rango de fechas calculado para la semana.

6.  **Selección de Datos**: Selecciona los campos relevantes para el reporte, incluyendo la fecha, horas de entrada/salida, quién lo registró, observaciones y una descripción concatenada del vehículo y la familia asociada.

7.  **Respuesta JSON**: Devuelve un objeto JSON con un `status` (`exito`) y un array `registros` que contiene los datos de los movimientos vehiculares.

---

## 3. Formato de la Respuesta (JSON)

La API devuelve un objeto JSON con la siguiente estructura:

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "exito",
  "registros": [
    {
      "fecha": "2023-10-25",
      "hora_entrada": "07:30:00",
      "hora_salida": "16:00:00",
      "registrado_por": "Admin",
      "observaciones": "",
      "descripcion": "ABC-123 - Sedan (Familia Pérez)"
    },
    {
      "fecha": "2023-10-26",
      "hora_entrada": "07:45:00",
      "hora_salida": null,
      "registrado_por": "Seguridad",
      "observaciones": "",
      "descripcion": "XYZ-987 - SUV (Familia García)"
    }
  ]
}
```
