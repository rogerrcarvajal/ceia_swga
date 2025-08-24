# Documentación del Archivo: `api/obtener_madre.php`

## 1. Propósito del Archivo

Este endpoint de API se dedica a obtener y devolver los datos de una madre específica, basándose en su ID. Es utilizado por la interfaz de gestión de expedientes para cargar la información de la madre de un estudiante seleccionado.

---

## 2. Lógica de Negocio y Flujo de Operación

El flujo es idéntico al de `obtener_padre.php`, pero orientado a la tabla `madres`.

1.  **Cabecera JSON**: Establece el `Content-Type` a `application/json`.
2.  **Conexión a BD**: Incluye `src/config.php`.
3.  **Recepción de Parámetro**: Espera un parámetro `id` en la URL (`$_GET['id']`), que corresponde al `madre_id`.
4.  **Validación**: Confirma que el `id` fue proporcionado.
5.  **Consulta Segura**: Ejecuta una consulta preparada (`SELECT * FROM madres WHERE madre_id = :id_madre`) para buscar a la madre.
6.  **Respuesta**: Devuelve los datos de la madre en formato JSON si se encuentra, o un JSON de error si no.
7.  **Manejo de Errores**: Utiliza un bloque `try...catch` para gestionar excepciones y devolver errores con un código HTTP 400.

---

## 3. Formato de la Respuesta (JSON)

### Respuesta Exitosa

Un objeto JSON con los datos de la madre.

**Ejemplo:**
```json
{
  "madre_id": "M002",
  "madre_nombre": "Ana",
  "madre_apellido": "López",
  "madre_cedula_pasaporte": "V-11234567",
  "...": "..."
}
```

### Respuesta de Error

Un objeto JSON con una clave `error`.

**Ejemplo:**
```json
{
  "error": "No se encontró información para la madre."
}
```
