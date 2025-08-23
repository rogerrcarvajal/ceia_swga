# Documentación del Archivo: `api/obtener_padre.php`

## 1. Propósito del Archivo

Este archivo es un endpoint de API cuya única función es obtener y devolver los datos de un padre específico. Es invocado por el frontend, específicamente por el script `admin_estudiantes.js`, cuando un administrador selecciona un estudiante y el sistema necesita cargar la información del padre asociado a ese estudiante.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Cabecera JSON**: El script se asegura de que la respuesta sea interpretada como JSON por el cliente.
2.  **Conexión a BD**: Incluye `src/config.php` para la conexión a la base de datos.
3.  **Recepción de Parámetro**: Espera un parámetro `id` en la URL (`$_GET['id']`), que corresponde al `padre_id` en la tabla `padres`.
4.  **Validación**: Verifica que el `id` no sea nulo. Si lo es, lanza una excepción.
5.  **Consulta Segura**: Ejecuta una consulta preparada (`SELECT * FROM padres WHERE padre_id = :id_padre`) para buscar al padre en la base de datos, previniendo inyección SQL.
6.  **Respuesta**: 
    *   Si se encuentra al padre, codifica el array asociativo con sus datos a formato JSON y lo devuelve.
    *   Si no se encuentra, devuelve un objeto JSON con un mensaje de error.
7.  **Manejo de Errores**: Un bloque `try...catch` gestiona cualquier excepción, devolviendo un código de estado HTTP 400 (Bad Request) y un mensaje de error en formato JSON.

---

## 3. Formato de la Respuesta (JSON)

### Respuesta Exitosa

Un objeto JSON con los datos del padre.

**Ejemplo:**
```json
{
  "padre_id": "P001",
  "padre_nombre": "Carlos",
  "padre_apellido": "Pérez",
  "padre_cedula_pasaporte": "V-10123456",
  "...": "..."
}
```

### Respuesta de Error

Un objeto JSON con una clave `error`.

**Ejemplo:**
```json
{
  "error": "No se encontró información para el padre."
}
```
