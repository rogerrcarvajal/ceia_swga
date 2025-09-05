# Documentación del Archivo: `api/consultar_movimiento_staff.php`

## 1. Propósito del Archivo

Este archivo es el endpoint de la API que alimenta la página de consulta de asistencia del personal (`gestion_es_staff.php`). Su función es recibir criterios de filtrado (una semana y/o un miembro del staff), consultar la base de datos para encontrar los registros de asistencia correspondientes y devolverlos en formato JSON.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetros**: El script captura los parámetros `semana` y `staff_id` (opcional) de la URL (`GET`).

2.  **Cálculo del Rango de Fechas**: Si se proporciona el parámetro `semana` (con formato `YYYY-W##`), el script lo utiliza para calcular la fecha de inicio (lunes) y de fin (domingo) de esa semana en particular. Esto permite filtrar los registros de la base de datos en ese rango de fechas exacto.

3.  **Construcción Dinámica de la Consulta**: El script construye una consulta SQL de forma dinámica y segura:
    *   La consulta base es un `SELECT` sobre la tabla `entrada_salida_staff`, que se une (`JOIN`) con la tabla `profesores` para poder mostrar el nombre completo del personal.
    *   Se añade una cláusula `WHERE` dinámicamente. Si se especificó una semana, se añade una condición `fecha BETWEEN :week_start AND :week_end`. Si se especificó un `staff_id`, se añade una condición `profesor_id = :staff_id`.
    *   Ambas condiciones se pueden combinar con `AND` si se proporcionan ambos filtros.

4.  **Ejecución Segura**: La consulta se ejecuta utilizando un *statement preparado*, pasando los valores de los filtros en el array de `execute()`. Esto previene cualquier riesgo de inyección SQL.

5.  **Respuesta JSON**: El script codifica el resultado de la consulta en un objeto JSON y lo devuelve al cliente. La respuesta siempre tiene una clave `status` y una clave `data` que contiene el array de resultados.

---

## 3. Tabla de Datos Clave

La fuente principal de información para esta API es la tabla `entrada_salida_staff`. Esta tabla parece estar diseñada para almacenar un registro por día por cada miembro del personal, conteniendo campos como `hora_entrada`, `hora_salida` y un booleano `ausente`. La existencia de esta tabla sugiere que otro proceso (probablemente en `pages/control_acceso.php`) se encarga de registrar estos movimientos diariamente.

---

## 4. Formato de la Respuesta (JSON)

La API devuelve un objeto JSON con dos claves principales.

*   `status`: Será `'ok'` si la consulta fue exitosa (incluso si no arrojó resultados) o `'error'` si hubo una excepción.
*   `data`: Un array de objetos, donde cada objeto representa un registro de asistencia.

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "ok",
  "data": [
    {
      "nombre_completo": "Juan Pérez",
      "fecha": "2023-10-23",
      "hora_entrada": "08:02:15",
      "hora_salida": "17:05:00",
      "ausente": false
    },
    {
      "nombre_completo": "Maria Rodriguez",
      "fecha": "2023-10-23",
      "hora_entrada": null,
      "hora_salida": null,
      "ausente": true
    }
  ]
}
```
