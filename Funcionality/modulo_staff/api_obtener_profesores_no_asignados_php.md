# Documentación del Archivo: `api/obtener_profesores_no_asignados.php`

## 1. Propósito del Archivo

Este endpoint de API está diseñado para devolver una lista en formato JSON de todos los miembros del staff que **no están asignados** a un período escolar específico. Es útil para interfaces que necesitan presentar una selección de personal disponible para ser matriculado en un período.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetro**: El script espera recibir el `periodo_id` a través de una solicitud `GET`.
2.  **Consulta con Subconsulta (NOT IN)**: Ejecuta una consulta SQL que selecciona `id` y `nombre_completo` de la tabla `profesores`.
    *   La clave de la lógica es la cláusula `WHERE id NOT IN (...)`, que utiliza una subconsulta para excluir a todos los profesores cuyos `id` ya aparecen en la tabla `profesor_periodo` para el `periodo_id` dado.
3.  **Ordenamiento**: Los resultados se ordenan por el nombre completo del profesor.
4.  **Respuesta JSON**: Devuelve el resultado como un array de objetos JSON. Si todos los profesores ya están asignados al período o no hay profesores, devuelve un array JSON vacío.

---

## 3. Formato de la Respuesta (JSON)

Un array de objetos, donde cada objeto representa a un miembro del staff no asignado al período.

**Ejemplo de respuesta:**
```json
[
  {
    "id": "P007",
    "nombre_completo": "Prof. Roberto Salas"
  },
  {
    "id": "P012",
    "nombre_completo": "Sra. Carmen Díaz"
  }
]
```
