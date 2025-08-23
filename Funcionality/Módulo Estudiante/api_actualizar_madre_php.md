# Documentación del Archivo: `api/actualizar_madre.php`

## 1. Propósito del Archivo

Este endpoint de API es el responsable de actualizar la información de una madre en la base de datos, procesando los datos que se envían desde el formulario "Datos de la Madre" en la página de gestión de expedientes.

---

## 2. Lógica de Negocio y Flujo de Operación

El flujo es prácticamente idéntico al de `actualizar_padre.php`:

1.  **Verificación de Método**: Responde únicamente a solicitudes `POST`.
2.  **Recepción de Datos**: Recibe los datos del formulario de la madre vía `$_POST`.
3.  **Validación de ID**: Asegura que el `madre_id` esté presente en la solicitud.
4.  **Lógica de Cédula (Inmutable)**: Al igual que con el padre, la cédula de la madre no se puede modificar. El script primero consulta la cédula actual en la base de datos y la utiliza, ignorando cualquier valor que venga del formulario.
5.  **Consulta de Actualización (UPDATE)**: Ejecuta una consulta `UPDATE` preparada y segura en la tabla `madres`.
6.  **Respuesta JSON**: Informa el resultado mediante un objeto JSON con una clave `success` o `error`.

---

## 3. Inconsistencias Notadas

*   **Formato de Respuesta**: Al igual que la API del padre, el formato de respuesta no está estandarizado con el de las otras APIs del módulo.
*   **Mensajes de Error**: Se corrigieron mensajes de error que incorrectamente mencionaban "padre" en lugar de "madre" y un error tipográfico en el mensaje de éxito.

---

## 4. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` con los datos del formulario `#form_madre`.

---

## 5. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "success": "Datos de la madre actualizados correctamente"
}
```

**Ejemplo de respuesta de error:**
```json
{
  "error": "Madre no encontrada"
}
```
