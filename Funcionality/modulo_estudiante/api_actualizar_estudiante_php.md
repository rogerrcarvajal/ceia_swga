# Documentación del Archivo: `api/actualizar_estudiante.php`

## 1. Propósito del Archivo

Este endpoint de API está dedicado a procesar las actualizaciones de la información personal de un estudiante. Recibe los datos enviados desde el formulario de estudiante en la página de gestión de expedientes y los persiste en la base de datos.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Verificación de Método**: El script primero comprueba que la solicitud se haya realizado utilizando el método HTTP `POST`, que es el método estándar para enviar datos de formulario.
2.  **Recepción de Datos**: Todos los datos del formulario son recibidos a través del array superglobal `$_POST`.
3.  **Validación de ID**: Se realiza una validación crítica para asegurar que `$_POST['id']` (el ID del estudiante) no esté vacío. Sin este ID, es imposible saber qué registro actualizar.
4.  **Consulta de Actualización (UPDATE)**:
    *   Se define una consulta SQL `UPDATE` para la tabla `estudiantes`.
    *   La consulta incluye una lista de todos los campos que se pueden modificar, utilizando placeholders (ej. `:nombre_completo`) para cada valor.
    *   La cláusula `WHERE id = :id` asegura que solo se actualice el registro del estudiante correcto.
5.  **Ejecución Segura**: La consulta se ejecuta utilizando un *statement preparado*. Los valores de `$_POST` se vinculan a los placeholders en el método `execute()`. Esto previene inyecciones SQL y maneja la sanitización de los datos.
    *   **Manejo de Checkboxes**: Para los campos booleanos como `staff`, se usa `isset($_POST['staff']) ? 1 : 0` para convertir el estado del checkbox (marcado/no marcado) a un valor numérico (1 o 0) para la base de datos.
    *   **Valores por Defecto**: Se utiliza el operador de fusión de null (`??`) y operadores ternarios para asignar valores por defecto (como strings vacíos o `null`) a los campos que podrían no haber sido enviados por el formulario, evitando errores.
6.  **Respuesta JSON**: El script devuelve un objeto JSON con un `status` (`exito` o `error`) y un `message` descriptivo para informar al frontend sobre el resultado de la operación.

---

## 3. Formato de la Petición (Request)

*   **Método**: `POST`
*   **Cuerpo (Body)**: `FormData` conteniendo los pares clave-valor de los campos del formulario `#form_estudiante`.

---

## 4. Formato de la Respuesta (JSON)

**Ejemplo de respuesta exitosa:**
```json
{
  "status": "exito",
  "message": "✅ Datos del estudiante actualizados correctamente."
}
```

**Ejemplo de respuesta de error:**
```json
{
  "status": "error",
  "message": "Error de base de datos: ..."
}
```
