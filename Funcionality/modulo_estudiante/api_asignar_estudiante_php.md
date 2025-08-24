# Documentación del Archivo: `api/asignar_estudiante.php`

## 1. Propósito del Archivo

Este endpoint de API es el motor de la funcionalidad de asignación. Su responsabilidad es recibir los datos del formulario de asignación masiva y **crear o actualizar** el registro que vincula a un estudiante con un período escolar y un grado específico en la tabla `estudiante_periodo`.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: Primero, verifica que el usuario esté autenticado y tenga el rol de `master` o `admin`.
2.  **Verificación de Método**: Solo procesa solicitudes `POST`.
3.  **Recepción de Datos**: Obtiene `periodo_id`, `estudiante_id` y `grado_cursado` del cuerpo de la solicitud `POST`.
4.  **Lógica de "Upsert" (Update or Insert)**: El script implementa una lógica de "actualizar o insertar" para manejar la asignación de forma robusta:
    *   Primero, realiza una consulta `SELECT` para verificar si ya existe una fila en `estudiante_periodo` para la combinación de `estudiante_id` y `periodo_id`.
    *   **Si existe**: Significa que el estudiante ya estaba asignado (posiblemente con un grado nulo). El script ejecuta una consulta `UPDATE` para establecer o cambiar el `grado_cursado` de ese registro existente.
    *   **Si no existe**: Significa que es una asignación completamente nueva. El script ejecuta una consulta `INSERT` para crear la nueva fila que vincula al estudiante, el período y el grado.
5.  **Respuesta JSON**: Devuelve una respuesta estandarizada con un `status` (`exito` o `error`) y un `message` para informar al frontend del resultado de la operación.

---

## 3. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` con los campos `periodo_id`, `estudiante_id` y `grado_cursado`.

---

## 4. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "exito",
  "message": "Estudiante asignado al período exitosamente."
}
```

**Ejemplo de respuesta de error:**
```json
{
  "status": "error",
  "message": "Datos incompletos."
}
```
