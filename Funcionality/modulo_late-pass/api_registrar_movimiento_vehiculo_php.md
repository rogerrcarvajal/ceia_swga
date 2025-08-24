# Documentación del Archivo: `api/registrar_movimiento_vehiculo.php`

## 1. Propósito del Archivo

Este endpoint de API es el encargado de registrar los **movimientos de entrada y salida de vehículos** en el plantel. Forma parte del sistema de control de acceso y su lógica determina si un escaneo de QR corresponde a una entrada o a una salida, basándose en el último movimiento registrado para ese vehículo.

---

## 2. Lógica de Negocio y Flujo de Operación

Todo el proceso se ejecuta dentro de una **transacción de base de datos** para asegurar la atomicidad de las operaciones.

1.  **Recepción y Validación**: El script espera una solicitud `POST` con un parámetro `codigo` (ej. `VEH-123`). Valida que el código tenga el prefijo `VEH-` y extrae el `vehiculo_id`.

2.  **Obtención de Contexto**: 
    *   Verifica la sesión del usuario que registra el movimiento.
    *   Consulta la base de datos para obtener los detalles del vehículo (placa, modelo) y el nombre del estudiante asociado a él, asegurándose de que el vehículo esté vinculado a un estudiante en el período activo.
    *   Establece la zona horaria a 'America/Caracas' y obtiene la fecha y hora actuales.

3.  **Determinación de Movimiento (Entrada/Salida)**: 
    *   El script consulta la tabla `registro_vehiculos` para buscar un registro para el `vehiculo_id` en la `fecha_actual` donde la `hora_salida` sea `NULL`. Este es un "registro abierto" que indica que el vehículo entró pero aún no ha salido.
    *   **Si existe un registro abierto**: El sistema asume que este escaneo corresponde a una **Salida**. Ejecuta un `UPDATE` sobre ese registro para rellenar la `hora_salida`.
    *   **Si no existe un registro abierto**: El sistema asume que este escaneo corresponde a una **Entrada**. Ejecuta un `INSERT` para crear un nuevo registro con la `hora_entrada`.

4.  **Respuesta Final**: Si todas las operaciones son exitosas, la transacción se confirma (`commit`) y se devuelve una respuesta JSON detallada al frontend para que la función `mostrarMensaje` pueda construir el feedback visual.

---

## 3. Formato de la Respuesta (JSON)

La respuesta está diseñada para darle al frontend toda la información que necesita para mostrar un mensaje claro al operador.

**Ejemplo de respuesta exitosa (entrada):**
```json
{
  "success": true,
  "message": "✅ Entrada registrada para vehículo ABC-123.",
  "data": {
    "tipo": "VEH",
    "nombre_completo": "Juan Pérez",
    "grado": "Grade 7",
    "placa": "ABC-123",
    "modelo": "Toyota Corolla",
    "hora_registrada": "07:45:00",
    "tipo_movimiento": "Entrada"
  }
}
```

**Ejemplo de respuesta exitosa (salida):**
```json
{
  "success": true,
  "message": "✅ Salida registrada para vehículo XYZ-987.",
  "data": {
    "tipo": "VEH",
    "nombre_completo": "María García",
    "grado": "Grade 3",
    "placa": "XYZ-987",
    "modelo": "Honda Civic",
    "hora_registrada": "16:30:00",
    "tipo_movimiento": "Salida"
  }
}
```
