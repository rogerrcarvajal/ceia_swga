# Documentación del Archivo: `api/obtener_estudiante.php`

## 1. Propósito del Archivo

Este archivo es un **endpoint de API** (Interfaz de Programación de Aplicaciones) dedicado. Su única responsabilidad es recibir una solicitud con el ID de un estudiante, consultar la base de datos para encontrar la información completa de ese estudiante y devolverla en un formato estructurado (JSON) que pueda ser fácilmente interpretado por el código del frontend (como `admin_estudiantes.js`).

---

## 2. Lógica de Negocio y Flujo de Operación

El script sigue un flujo de ejecución lineal y seguro para cumplir con su propósito:

1.  **Establecer Cabecera de Respuesta**: Lo primero que hace el script es declarar que su respuesta será en formato JSON, estableciendo la cabecera HTTP `Content-Type: application/json`. Esto es crucial para que el navegador y el código JavaScript que realiza la llamada `fetch` interpreten correctamente la respuesta.

2.  **Incluir Configuración**: Importa el archivo `src/config.php` para tener acceso a la variable de conexión a la base de datos (`$conn`).

3.  **Recepción del Parámetro**: El script espera recibir el ID del estudiante a través de un parámetro en la URL (un `GET` request). Busca específicamente `$_GET['id']`.

4.  **Validación de Entrada**: Comprueba si el `id` fue realmente proporcionado. Si no se encuentra, lanza una excepción con el mensaje "ID de estudiante no proporcionado", lo que interrumpe el flujo normal y es capturado por el bloque `catch`.

5.  **Consulta Segura a la Base de Datos**:
    *   **Preparación**: Utiliza un *statement preparado* (`$conn->prepare(...)`) para la consulta SQL. Este es el método más recomendado para interactuar con la base de datos, ya que previene ataques de **inyección SQL**.
    *   **Ejecución**: Ejecuta la consulta pasando el `id` recibido como un parámetro vinculado (`':id' => $id`). Esto asegura que el valor del ID es tratado como un dato y no como parte del código SQL.
    *   **Obtención de Datos**: Utiliza `fetch(PDO::FETCH_ASSOC)` para obtener una única fila de resultado como un array asociativo (donde las claves son los nombres de las columnas de la tabla `estudiantes`).

6.  **Construcción de la Respuesta**: Se evalúa el resultado de la consulta:
    *   **Si se encuentra un estudiante**: La respuesta JSON contendrá todos los datos de ese estudiante.
    *   **Si no se encuentra**: La respuesta JSON contendrá un mensaje indicando que el estudiante no fue encontrado.

7.  **Manejo de Errores (`try...catch`)**: Todo el proceso está envuelto en un bloque `try...catch`. Si ocurre cualquier error durante la validación o la consulta a la base de datos, el bloque `catch` se activa, y la respuesta JSON contendrá un mensaje de error claro.

---

## 3. Formato de la Respuesta (JSON)

La API siempre devuelve una respuesta en formato JSON.

### Respuesta Exitosa

Si se encuentra el estudiante, la respuesta es un objeto JSON que representa al estudiante, con pares clave-valor que corresponden a las columnas de la tabla `estudiantes`.

**Ejemplo de respuesta exitosa:**
```json
{
  "id": "12345678",
  "nombre_completo": "Ana Sofía",
  "apellido_completo": "Pérez López",
  "fecha_nacimiento": "2010-05-15",
  "padre_id": "P001",
  "madre_id": "M002",
  "staff": false,
  "...": "..."
}
```

### Respuesta de Error

Si no se proporciona un ID, no se encuentra al estudiante o hay un error en la base de datos, la respuesta es un objeto JSON con una única clave, `error`.

**Ejemplo de respuesta de error:**
```json
{
  "error": "Estudiante no encontrado con el ID proporcionado."
}
```
