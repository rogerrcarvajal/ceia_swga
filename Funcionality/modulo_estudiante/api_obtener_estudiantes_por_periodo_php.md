# Documentación del Archivo: `api/obtener_estudiantes_por_periodo.php`

## 1. Propósito del Archivo

Este endpoint de API tiene una única función: devolver una lista en formato JSON de todos los estudiantes que ya han sido **asignados** a un período escolar específico. Es utilizado por la página de asignación masiva para poblar el panel izquierdo, mostrando al administrador quiénes ya están matriculados en el período seleccionado.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetro**: El script espera recibir el `periodo_id` a través de una solicitud `GET`.
2.  **Consulta con JOIN**: Ejecuta una consulta SQL que une (`JOIN`) dos tablas:
    *   `estudiante_periodo`: La tabla que contiene las relaciones entre estudiantes y períodos.
    *   `estudiantes`: La tabla que contiene los nombres y apellidos de los estudiantes.
3.  **Filtrado**: La consulta se filtra con una cláusula `WHERE ep.periodo_id = :periodo_id` para obtener únicamente los registros del período de interés.
4.  **Selección de Datos**: Selecciona los campos más importantes para la visualización: el ID del estudiante, su nombre, su apellido y, crucialmente, el `grado_cursado` en ese período.
5.  **Respuesta JSON**: Devuelve el resultado como un array de objetos JSON. Si no se encuentran estudiantes, devuelve un array JSON vacío `[]`.

---

## 3. Formato de la Respuesta (JSON)

Un array de objetos, donde cada objeto representa a un estudiante asignado.

**Ejemplo de respuesta:**
```json
[
  {
    "id": "V12345678",
    "nombre_completo": "Ana Sofía",
    "apellido_completo": "Pérez López",
    "grado_cursado": "Grade 5"
  },
  {
    "id": "V87654321",
    "nombre_completo": "Luis Miguel",
    "apellido_completo": "García Torres",
    "grado_cursado": "Grade 3"
  }
]
```
