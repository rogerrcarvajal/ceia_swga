# Documentación del Archivo: `api/obtener_profesores.php`

## 1. Propósito del Archivo

Este endpoint de API tiene como objetivo principal devolver una lista en formato JSON de todos los miembros del staff que ya han sido **asignados** a un período escolar específico. Es probable que sea utilizado por una interfaz que necesite mostrar el personal matriculado en un período determinado.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetro**: El script espera recibir el `periodo_id` a través de una solicitud `GET`.
2.  **Consulta con JOIN**: Ejecuta una consulta SQL que une (`JOIN`) las tablas `profesor_periodo` y `profesores`. Esto permite obtener los datos del profesor junto con la información de su asignación para el período.
3.  **Filtrado**: La consulta se filtra con una cláusula `WHERE pp.periodo_id = :periodo_id` para obtener únicamente los registros del período de interés.
4.  **Selección de Datos**: Selecciona los campos relevantes para la visualización: `asignacion_id` (el ID del registro en `profesor_periodo`), `nombre_completo`, `cedula`, `telefono`, `posicion` y `homeroom_teacher`.
5.  **Ordenamiento**: Los resultados se ordenan por el nombre completo del profesor.
6.  **Respuesta JSON**: Devuelve el resultado como un array de objetos JSON. Si no se encuentran profesores asignados, devuelve un array JSON vacío.

---

## 3. Formato de la Respuesta (JSON)

Un array de objetos, donde cada objeto representa a un miembro del staff asignado a un período.

**Ejemplo de respuesta:**
```json
[
  {
    "asignacion_id": "1",
    "nombre_completo": "Dr. Juan Pérez",
    "cedula": "V-12345678",
    "telefono": "0412-1234567",
    "posicion": "Director",
    "homeroom_teacher": "N/A"
  },
  {
    "asignacion_id": "5",
    "nombre_completo": "Lic. María García",
    "cedula": "V-87654321",
    "telefono": "0414-9876543",
    "posicion": "Grade 5",
    "homeroom_teacher": "Grade 5"
  }
]
```
