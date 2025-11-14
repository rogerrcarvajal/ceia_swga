# Documentación del Archivo: `api/obtener_estudiantes_no_asignados.php`

## 1. Propósito del Archivo

Este endpoint de API es el responsable de generar la lista de todos los estudiantes que están **disponibles para ser asignados** a un período escolar específico. El resultado de esta API se utiliza para rellenar el menú desplegable de estudiantes en el formulario de asignación de la página de gestión masiva.

---

## 2. Lógica de Negocio y Flujo de Operación

La lógica de esta API es más compleja que una simple consulta, ya que debe identificar a todos los estudiantes "asignables".

1.  **Recepción de Parámetro**: Recibe el `periodo_id` a través de una solicitud `GET`.
2.  **Consulta con UNION**: Para obtener una lista completa y sin duplicados, el script utiliza una consulta `UNION` que combina los resultados de dos consultas `SELECT` diferentes:
    *   **Primera Consulta**: Selecciona a todos los estudiantes cuyo `id` **no aparece en absoluto** en la tabla `estudiante_periodo`. Estos son, típicamente, estudiantes recién registrados que nunca han sido matriculados en ningún período.
    *   **Segunda Consulta**: Selecciona a los estudiantes que **sí tienen una entrada** en `estudiante_periodo` para el período seleccionado, pero cuyo campo `grado_cursado` está vacío o es nulo. Esto captura casos de asignaciones incompletas o erróneas.
3.  **Ordenamiento**: El resultado final de la `UNION` se ordena alfabéticamente para facilitar su uso en el menú desplegable del frontend.
4.  **Respuesta JSON**: Devuelve un array de objetos JSON, donde cada objeto es un estudiante disponible. Si no hay ninguno, devuelve un array vacío `[]`.

---

## 3. Formato de la Respuesta (JSON)

Un array de objetos, donde cada objeto representa a un estudiante que puede ser asignado.

**Ejemplo de respuesta:**
```json
[
  {
    "id": "V22334455",
    "nombre_completo": "Mariana",
    "apellido_completo": "Gómez"
  },
  {
    "id": "V99887766",
    "nombre_completo": "Pedro",
    "apellido_completo": "Martínez"
  }
]
```
