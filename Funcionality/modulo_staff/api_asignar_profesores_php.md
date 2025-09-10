# Documentación del Archivo: `api/asignar_profesores.php`

## 1. Propósito del Archivo

Este endpoint de API se encarga de **crear una nueva asignación** para un miembro del staff en un período escolar específico. Su función es insertar un nuevo registro en la tabla `profesor_periodo`, vinculando a un profesor con un período, una posición y, opcionalmente, un homeroom.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: Realiza una verificación de seguridad para asegurar que solo usuarios con rol `admin` o `master` puedan ejecutar esta acción.
2.  **Verificación de Método**: Solo procesa solicitudes `POST`.
3.  **Recepción de Parámetros**: Espera `profesor_id`, `periodo_id`, `posicion` y `homeroom_teacher` (opcional) en el cuerpo de la solicitud.
4.  **Manejo de Homeroom Nulo**: Si el valor de `homeroom_teacher` es vacío o 'N/A', se convierte a `NULL` para ser almacenado en la base de datos.
5.  **Inserción en Base de Datos**: Ejecuta una consulta `INSERT` preparada en la tabla `profesor_periodo`.
6.  **Manejo de Duplicados**: Si se intenta asignar al mismo profesor al mismo período más de una vez (lo que violaría una restricción de unicidad en la base de datos), el script captura el error `PDOException` con código `23505` y devuelve un mensaje de error específico al frontend.
7.  **Respuesta JSON**: Devuelve un objeto JSON con `status` (`success` o `error`) y un `message` descriptivo.

---

## 3. Observaciones de Uso

Esta API realiza una operación de inserción pura. La página `pages/gestionar_profesor.php` ya maneja la lógica de insertar o actualizar asignaciones. Esto sugiere que `api/asignar_profesores.php` podría ser utilizada por otra interfaz (quizás una página de asignación masiva de profesores, similar a la de estudiantes, o una funcionalidad de creación rápida de asignaciones).

---

## 4. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` con los campos `profesor_id`, `periodo_id`, `posicion` y `homeroom_teacher`.

---

## 5. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "success",
  "message": "Profesor asignado correctamente al período."
}
```

**Ejemplo de respuesta de error (duplicado):**
```json
{
  "status": "error",
  "message": "Error: Este profesor ya está asignado a este período escolar."
}
```
