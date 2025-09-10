# Documentación del Archivo: `api/actualizar_padre.php`

## 1. Propósito del Archivo

Este endpoint de API maneja la actualización de la información de un padre en la base de datos. Procesa los datos enviados desde el formulario de "Datos del Padre" en la interfaz de gestión de expedientes.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Verificación de Método**: Solo responde a solicitudes `POST`.
2.  **Recepción de Datos**: Obtiene los datos del formulario del padre desde `$_POST`.
3.  **Validación de ID**: Verifica que se haya proporcionado el `padre_id`.
4.  **Lógica de Cédula (Inmutable)**: Una característica de seguridad importante de este script es que **no permite modificar la cédula del padre**. Primero, realiza una consulta `SELECT` para obtener la cédula actual del padre desde la base de datos usando el `padre_id`. Luego, ignora cualquier valor de cédula que pudiera venir del formulario y utiliza el que ya existe en la base de datos. Esto previene que se altere el identificador único de la persona.
5.  **Consulta de Actualización (UPDATE)**: Construye y ejecuta una consulta `UPDATE` preparada para la tabla `padres`, actualizando todos los campos excepto la cédula.
6.  **Respuesta JSON**: Devuelve un objeto JSON simple con una clave `success` o `error` para notificar el resultado.

---

## 3. Inconsistencias Notadas

*   **Nombre del Archivo**: Originalmente nombrado `Actualizar_padre.php` con mayúscula, fue renombrado a `actualizar_padre.php` para mantener la consistencia.
*   **Formato de Respuesta**: El formato de respuesta (`{'success': '...'}`) difiere del formato (`{'status': '...', 'message': '...'}`) utilizado por las APIs de estudiante y ficha médica. Sería ideal estandarizarlo.

---

## 4. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` con los datos del formulario `#form_padre`.

---

## 5. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "success": "Datos del padre actualizados correctamente"
}
```

**Ejemplo de respuesta de error:**
```json
{
  "error": "Padre no encontrado"
}
```
