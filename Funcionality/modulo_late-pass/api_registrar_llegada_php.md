# Documentación del Archivo: `api/registrar_llegada.php`

## 1. Propósito del Archivo

Este endpoint de API es el encargado de registrar la **llegada diaria de un estudiante** al plantel. Forma parte del sistema de control de acceso y su lógica va más allá de un simple registro, ya que determina si la llegada es a tiempo o tarde y lleva un conteo de las tardanzas semanales ("strikes").

---

## 2. Lógica de Negocio y Flujo de Operación

Todo el proceso se ejecuta dentro de una **transacción de base de datos** para asegurar la atomicidad de las operaciones.

1.  **Recepción y Validación**: El script espera una solicitud `POST` con un parámetro `codigo` (ej. `EST-123`). Valida que el código tenga el prefijo `EST-` y extrae el `estudiante_id`.

2.  **Obtención de Contexto**: 
    *   Verifica que exista un período escolar activo.
    *   Consulta la base de datos para obtener el nombre completo y el grado del estudiante, asegurándose de que esté asignado al período activo.
    *   Establece la zona horaria a 'America/Caracas' y obtiene la fecha y hora actuales.

3.  **Control de Duplicados**: Antes de registrar, verifica si el estudiante ya tiene un registro de llegada para la fecha actual en la tabla `llegadas_tarde`. Si ya existe, lanza una excepción para evitar duplicados.

4.  **Registro de Llegada**: Inserta un nuevo registro en la tabla `llegadas_tarde` con el `estudiante_id`, la fecha, la hora de llegada y la semana del año.

5.  **Lógica de Tardanza y "Strikes"**: 
    *   Si la `hora_llegada` es posterior a las `08:06:00`, la llegada se considera **tarde**.
    *   Si es tarde, el script calcula el número de "strikes" (llegadas tarde) que el estudiante ha acumulado en la semana actual (de lunes a domingo). Esto se hace consultando la tabla `llegadas_tarde` para ese estudiante y ese rango de fechas, contando solo las llegadas que fueron después de las 08:06:00.
    *   El mensaje final de la respuesta se ajusta para indicar si la llegada fue a tiempo o tarde, y si fue tarde, cuántos strikes lleva.

6.  **Respuesta Final**: Si todas las operaciones son exitosas, la transacción se confirma (`commit`) y se devuelve una respuesta JSON detallada al frontend para que la función `mostrarMensaje` pueda construir el feedback visual.

---

## 3. Formato de la Respuesta (JSON)

La respuesta está diseñada para darle al frontend toda la información que necesita para mostrar un mensaje claro al operador.

**Ejemplo de respuesta exitosa (a tiempo):**
```json
{
  "success": true,
  "message": "✅ Llegada a tiempo registrada para Ana Pérez.",
  "data": {
    "tipo": "EST",
    "nombre_completo": "Ana Pérez",
    "grado": "Grade 5",
    "hora_registrada": "08:00:00",
    "strike_count": 0
  }
}
```

**Ejemplo de respuesta exitosa (tarde):**
```json
{
  "success": true,
  "message": "⚠️ LLEGADA TARDE para Juan García. Strike semanal #2.",
  "data": {
    "tipo": "EST",
    "nombre_completo": "Juan García",
    "grado": "Grade 7",
    "hora_registrada": "08:15:30",
    "strike_count": 2
  }
}
```
