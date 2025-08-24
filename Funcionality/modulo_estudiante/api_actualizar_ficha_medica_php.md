# Documentación del Archivo: `api/actualizar_ficha_medica.php`

## 1. Propósito del Archivo

Este endpoint de API se encarga de actualizar la ficha de salud de un estudiante en la tabla `salud_estudiantil`. Procesa los datos enviados desde el formulario de "Ficha Médica".

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Verificación de Método**: El script se ejecuta solo para solicitudes `POST`.
2.  **Recepción de Datos**: Recibe los datos del formulario a través de `$_POST`.
3.  **Validación de ID**: Valida que `$_POST['estudiante_id']` exista, ya que es la clave foránea que vincula la ficha con el estudiante y se usa en la cláusula `WHERE` para la actualización.
4.  **Consulta de Actualización (UPDATE)**:
    *   Define una consulta `UPDATE` para la tabla `salud_estudiantil`.
    *   La consulta está parametrizada con placeholders para prevenir inyección SQL.
5.  **Ejecución Segura**: Utiliza un *statement preparado* para ejecutar la actualización. 
    *   Maneja los valores de los checkboxes (como `dislexia`, `atencion`, etc.) convirtiéndolos a `1` o `0`.
    *   Proporciona valores por defecto para campos opcionales para evitar errores.
6.  **Respuesta JSON**: Devuelve un objeto JSON estandarizado con `status` y `message` para informar al frontend sobre el resultado.

---

## 3. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` conteniendo los datos del formulario `#form_ficha_medica`.

---

## 4. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "exito",
  "message": "✅ Ficha médica actualizada correctamente."
}
```

**Ejemplo de respuesta de error:**
```json
{
  "status": "error",
  "message": "Error: ID de Estudiante no proporcionado para actualizar la ficha."
}
```
