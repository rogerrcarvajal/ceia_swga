# Documentación del Archivo: `api/actualizar_profesores.php`

## 1. Propósito del Archivo

Este endpoint de API está diseñado para realizar **actualizaciones parciales y específicas** en los registros de asignación de personal (`profesor_periodo`). Su función principal es permitir la edición de un único campo (como la posición o el homeroom teacher) para una asignación ya existente, sin necesidad de recargar toda la información del profesor.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Verificación de Método**: Solo procesa solicitudes `POST`.
2.  **Recepción de Parámetros**: Espera tres parámetros en el cuerpo de la solicitud:
    *   `id`: El ID del registro específico en la tabla `profesor_periodo` que se desea actualizar.
    *   `field`: El nombre del campo de la tabla que se va a modificar (ej. `posicion`, `homeroom_teacher`).
    *   `value`: El nuevo valor para el campo.
3.  **Validación de Seguridad (Whitelist)**: Implementa una lista blanca (`$allowed_fields`) de los campos que pueden ser actualizados. Esto es una medida de seguridad crucial que previene que un atacante intente modificar campos no deseados en la base de datos.
4.  **Manejo de Valores Nulos**: Para el campo `homeroom_teacher`, si el `value` recibido es 'N/A' o una cadena vacía, se convierte a `NULL` antes de ser guardado en la base de datos. Esto asegura la consistencia de los datos.
5.  **Consulta de Actualización**: Construye y ejecuta una consulta `UPDATE` preparada dinámicamente, utilizando el nombre del campo y el valor proporcionados, junto con el ID del registro.
6.  **Respuesta JSON**: Devuelve un objeto JSON indicando el `status` (`success` o `error`) y un `message` con el resultado de la operación.

---

## 3. Observaciones de Uso

Este API parece estar diseñado para ser utilizado por una interfaz de usuario que permite la edición en línea o la actualización de campos individuales, en contraste con la página `pages/gestionar_profesor.php` que maneja la actualización de múltiples campos y la asignación en un solo envío de formulario.

---

## 4. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` con `id`, `field` y `value`.

---

## 5. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "success",
  "message": "Asignación actualizada."
}
```

**Ejemplo de respuesta de error:**
```json
{
  "status": "error",
  "message": "Datos incompletos o campo no permitido para edición."
}
```
