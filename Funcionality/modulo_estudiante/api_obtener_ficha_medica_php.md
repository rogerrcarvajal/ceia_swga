# Documentación del Archivo: `api/obtener_ficha_medica.php`

## 1. Propósito del Archivo

Este endpoint de API sirve para un propósito muy específico: obtener la ficha de salud de un estudiante. A diferencia de las APIs de padre y madre, esta se consulta directamente con el ID del estudiante, no con un ID propio de la ficha.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Cabecera JSON**: Establece el `Content-Type` a `application/json`.
2.  **Conexión a BD**: Incluye `src/config.php`.
3.  **Recepción de Parámetro**: Espera un parámetro `estudiante_id` en la URL (`$_GET['estudiante_id']`). Este es el identificador que vincula la ficha con el estudiante.
4.  **Validación**: Verifica que el `estudiante_id` haya sido enviado.
5.  **Consulta Segura**: Ejecuta una consulta preparada (`SELECT * FROM salud_estudiantil WHERE estudiante_id = :id_ficha`) para buscar la ficha médica.
6.  **Respuesta**: Devuelve los datos de la ficha médica en formato JSON si se encuentra, o un JSON de error si no.
7.  **Manejo de Errores**: Utiliza un bloque `try...catch` para gestionar excepciones.

---

## 3. Formato de la Respuesta (JSON)

### Respuesta Exitosa

Un objeto JSON con los datos de la ficha médica.

**Ejemplo:**
```json
{
  "id": "15",
  "estudiante_id": "12345678",
  "completado_por": "Ana López",
  "dislexia": true,
  "atencion": false,
  "...": "..."
}
```

### Respuesta de Error

Un objeto JSON con una clave `error`.

**Ejemplo:**
```json
{
  "error": "No se encontró información de la ficha Medica."
}
```
