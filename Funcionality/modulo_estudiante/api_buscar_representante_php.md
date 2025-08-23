# Documentación del Archivo: `api/buscar_representante.php`

## 1. Propósito del Archivo

Este archivo es un endpoint de API dinámico que sirve como motor para la funcionalidad de "búsqueda inteligente" de representantes en el formulario de inscripción (`planilla_inscripcion.php`). Su objetivo es verificar si un padre o una madre ya existe en la base de datos basándose en su número de cédula, para así evitar la creación de registros duplicados.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Recepción de Parámetros**: El script espera dos parámetros a través de la URL (método `GET`):
    *   `tipo`: Una cadena de texto que debe ser `'padre'` o `'madre'`. Este parámetro determina en qué tabla y columnas se realizará la búsqueda.
    *   `cedula`: El número de cédula del representante que se desea buscar.

2.  **Validación de Seguridad**: Antes de construir la consulta, el script realiza una validación crucial: se asegura de que el valor de `tipo` sea exactamente `'padre'` o `'madre'`. Esta es una medida de seguridad importante para prevenir que un usuario malintencionado pueda manipular el parámetro `tipo` para intentar consultar otras tablas de la base de datos.

3.  **Construcción Dinámica de la Consulta**: Basándose en el parámetro `tipo`, el script construye dinámicamente los nombres de la tabla y las columnas que necesita para la consulta. 
    *   Si `tipo` es `'padre'`, buscará en la tabla `padres`.
    *   Si `tipo` es `'madre'`, buscará en la tabla `madres`.
    Esto hace que el código sea reutilizable y más fácil de mantener.

4.  **Consulta Segura**: Ejecuta una consulta `SELECT` utilizando un *statement preparado* para buscar un registro que coincida con la cédula proporcionada. El uso de `prepare` y `execute` previene ataques de inyección SQL.

5.  **Construcción de la Respuesta**: Analiza el resultado de la consulta y genera una respuesta JSON.

---

## 3. Formato de la Respuesta (JSON)

La respuesta de la API está diseñada para ser simple y fácil de interpretar por el JavaScript del frontend.

### Representante Encontrado

Si la cédula existe en la tabla correspondiente, la respuesta incluye la bandera `encontrado` como `true` y los datos básicos para la vinculación.

**Ejemplo:**
```json
{
  "encontrado": true,
  "id": 12,
  "nombre": "Carlos Pérez"
}
```

### Representante No Encontrado

Si la cédula no existe, simplemente devuelve la bandera `encontrado` como `false`.

**Ejemplo:**
```json
{
  "encontrado": false
}
```

### Error en la Solicitud

Si faltan parámetros, el tipo no es válido o hay un error en la base de datos, la respuesta también incluye la bandera `encontrado` como `false` y una clave `error` con un mensaje descriptivo.

**Ejemplo:**
```json
{
  "encontrado": false,
  "error": "Tipo de representante no válido"
}
```
