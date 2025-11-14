# Documentación del Archivo: `api/registrar_movimiento_staff.php`

## 1. Propósito del Archivo

Este endpoint de API es el backend para el registro de movimientos del personal en el sistema de control de acceso. Su función no es solo guardar un registro, sino **aplicar la política de asistencia diaria** de la institución, determinando si un escaneo de QR corresponde a una entrada o a una salida y validando que ocurra en el momento adecuado del día.

---

## 2. Lógica de Negocio y Flujo de Operación

Todo el proceso se ejecuta dentro de una **transacción de base de datos**, lo que garantiza que las operaciones son atómicas: o se completan todos los pasos, o no se realiza ningún cambio, evitando inconsistencias.

1.  **Recepción y Validación**: El script espera una solicitud `POST` con un parámetro `codigo` (ej. `STF-123`). Valida que el código tenga el prefijo `STF-` y extrae el ID numérico del personal.

2.  **Obtención de Contexto**: 
    *   Establece la zona horaria a 'America/Caracas'.
    *   Obtiene la fecha y hora actuales.
    *   Consulta la base de datos para obtener el nombre y la posición del miembro del staff, que se usarán en la respuesta.

3.  **Consulta del Estado Actual**: El script consulta la tabla `entrada_salida_staff` para ver cuántos registros existen para ese `staff_id` en la `fecha_actual`.

4.  **Máquina de Estados de Asistencia**: Basado en el resultado de la consulta anterior, el script aplica la siguiente política:

    *   **Si no hay registros para hoy (`count == 0`)**: 
        *   El sistema asume que este es el **primer movimiento del día (Entrada)**.
        *   **Validación**: Comprueba que la hora actual sea **anterior a las 12:00 PM**. Si es más tarde, lanza un error, forzando a que la entrada se registre por la mañana.
        *   **Acción**: Si la hora es válida, ejecuta un `INSERT` en `entrada_salida_staff`, rellenando los campos `profesor_id`, `fecha` y `hora_entrada`.

    *   **Si ya existe un registro para hoy (`count == 1`)**: 
        *   El sistema asume que este es el **segundo movimiento del día (Salida)**.
        *   **Validación 1**: Comprueba si el campo `hora_salida` de ese registro ya tiene un valor. Si es así, significa que el ciclo del día ya se completó y lanza un error.
        *   **Validación 2**: Comprueba que la hora actual sea **posterior a las 12:00 PM**. Si es más temprano, lanza un error, forzando a que la salida se registre por la tarde.
        *   **Acción**: Si las validaciones pasan, ejecuta un `UPDATE` sobre el registro existente para rellenar el campo `hora_salida`.

    *   **Si hay más de un registro**: Se considera un estado anómalo y se lanza un error indicando que el ciclo de entrada/salida del día ya fue completado.

5.  **Respuesta Final**: Si todas las operaciones son exitosas, la transacción se confirma (`commit`) y se devuelve una respuesta JSON detallada al frontend para que la función `mostrarMensaje` pueda construir el feedback visual.

---

## 3. Formato de la Respuesta (JSON)

La respuesta está diseñada para darle al frontend toda la información que necesita.

**Ejemplo de respuesta exitosa:**
```json
{
  "success": true,
  "message": "✅ Entrada registrada para Juan Pérez.",
  "data": {
    "tipo": "STF",
    "nombre_completo": "Juan Pérez",
    "posicion": "IT Manager",
    "hora_registrada": "08:30:15",
    "tipo_movimiento": "Entrada"
  }
}
```

**Ejemplo de respuesta de error:**
```json
{
  "success": false,
  "message": "La segunda lectura (Salida) debe realizarse después de las 12:00 PM.",
  "data": null
}
```
