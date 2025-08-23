# Documentación del Archivo: `pages/eliminar_profesor.php`

## 1. Propósito del Archivo

Este es un script de backend sin interfaz gráfica. Su única y crítica función es **eliminar permanentemente un registro de un miembro del staff** de la base de datos. Se invoca al hacer clic en el enlace "Eliminar" que debería existir en la página de gestión de personal.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Control de Acceso**: El script primero valida la sesión y el rol del usuario para asegurar que solo un `master` o `admin` pueda realizar esta acción.
2.  **Recepción de ID**: Obtiene el `id` del miembro del staff a eliminar desde el parámetro `GET` en la URL.
3.  **Ejecución de la Eliminación**: 
    *   Prepara y ejecuta una consulta `DELETE` directa sobre la tabla `profesores`, utilizando el `id` recibido en la cláusula `WHERE`.
    *   La operación está envuelta en un bloque `try...catch` para manejar posibles errores de la base de datos, aunque actualmente no se informa de ellos al usuario.
4.  **Redirección**: Inmediatamente después de ejecutar la consulta (sin importar el resultado), el script redirige al usuario de vuelta a la página principal del módulo de personal.

---

## 3. Detalles de Implementación Clave

*   **Borrado en Cascada**: El código incluye un comentario que revela un detalle muy importante del diseño de la base de datos: `Gracias a "ON DELETE CASCADE" en la tabla 'profesor_periodo'...`. Esto significa que la base de datos está configurada para que, al eliminar un registro de la tabla `profesores`, se borren automáticamente todos los registros asociados en la tabla `profesor_periodo` (sus asignaciones a períodos escolares). Esto es una excelente práctica que mantiene la integridad referencial de los datos.

---

## 4. Puntos de Mejora y Observaciones

*   **Falta de Feedback**: El script no establece ningún mensaje de sesión (`$_SESSION['mensaje']`) para informar al usuario si la eliminación fue exitosa o si falló. El usuario es simplemente redirigido y no sabe si la operación se completó.
*   **URL de Redirección Incorrecta**: El script redirige a `profesores.php`, pero la página principal de gestión de este módulo es `profesores_registro.php`. Esto es probablemente un error tipográfico y debería ser corregido para que el usuario regrese a la página correcta.
