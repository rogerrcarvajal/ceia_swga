# Documentación del Archivo: `api/consultar_latepass.php`

## 1. Propósito del Archivo

Este endpoint de API es el encargado de proporcionar los datos para la página de "Gestión y consulta de Late-Pass" (`pages/gestion_latepass.php`). Su función es consultar la base de datos para obtener un listado detallado de las llegadas tarde de los estudiantes, permitiendo filtrar por semana y, opcionalmente, por grado. Además, integra información sobre el conteo de tardanzas semanales ("strikes").

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: El script inicia con una verificación de seguridad para asegurar que solo usuarios autenticados puedan acceder a la información.
2.  **Recepción de Parámetros**: Obtiene los parámetros `semana` (número de semana ISO) y `grado` (opcional, puede ser 'todos') de la URL (`GET`).
3.  **Verificación de Período Activo**: Consulta la base de datos para obtener el ID del período escolar activo, ya que todas las consultas se realizan en el contexto de este período.
4.  **Consulta SQL Compleja**: La API ejecuta una consulta SQL avanzada que involucra varias tablas para recopilar toda la información necesaria:
    *   **`llegadas_tarde`**: La tabla principal que contiene cada registro individual de llegada tarde.
    *   **`estudiantes`**: Se une para obtener el nombre y apellido completo del estudiante.
    *   **`estudiante_periodo`**: Se une para obtener el `grado_cursado` del estudiante en el período activo y asegurar que el estudiante esté matriculado en el período correcto.
    *   **`latepass_resumen_semanal` (LEFT JOIN)**: Esta es una tabla clave. Se realiza un `LEFT JOIN` para obtener el `conteo_tardes` (número de strikes) y el `ultimo_mensaje` asociado a las tardanzas de ese estudiante en esa semana específica. El uso de `COALESCE(rs.conteo_tardes, 0)` asegura que si no hay un resumen semanal para un estudiante, su conteo de tardanzas se muestre como 0.
5.  **Filtrado Dinámico**: La cláusula `WHERE` filtra los registros por el número de `semana` y el `periodo_id`. Si el parámetro `grado` no es 'todos', se añade una condición adicional para filtrar por el grado específico.
6.  **Ordenamiento**: Los resultados se ordenan por fecha y hora de llegada de forma descendente.
7.  **Respuesta JSON**: Devuelve un objeto JSON con un `status` (`exito` o `error`) y un array `registros` que contiene los datos de las llegadas tarde.

---

## 3. Tablas de Datos Clave

*   **`llegadas_tarde`**: Almacena cada registro individual de llegada de un estudiante.
*   **`latepass_resumen_semanal`**: Contiene un resumen semanal de las tardanzas de cada estudiante, incluyendo el conteo de strikes y un mensaje asociado. Esta tabla es probablemente actualizada por un proceso separado o por la misma API `registrar_llegada.php` (aunque en la versión actual de `registrar_llegada.php` solo se calcula el strike count al vuelo y no se actualiza esta tabla de resumen).

---

## 4. Formato de la Respuesta (JSON)

La API devuelve un objeto JSON con la siguiente estructura:

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "exito",
  "registros": [
    {
      "nombre_completo": "Ana",
      "apellido_completo": "García",
      "grado_cursado": "Grade 3",
      "fecha_registro": "2023-10-25",
      "hora_llegada": "08:10:00",
      "conteo_tardes": 2,
      "ultimo_mensaje": "Segunda tardanza de la semana."
    },
    {
      "nombre_completo": "Pedro",
      "apellido_completo": "Martínez",
      "grado_cursado": "Grade 5",
      "fecha_registro": "2023-10-24",
      "hora_llegada": "08:05:00",
      "conteo_tardes": 1,
      "ultimo_mensaje": null
    }
  ]
}
```
