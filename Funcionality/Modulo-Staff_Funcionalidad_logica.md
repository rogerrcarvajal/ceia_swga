# Análisis de Funcionalidad y Lógica: Módulo Staff

Este documento detalla el análisis del flujo de trabajo y la lógica de negocio para el Módulo de Staff (Profesores y Personal).

## Componentes Analizados

- **`pages/profesores_registro.php`**: Panel central para registrar nuevo personal y listar a todo el personal existente.
- **`pages/gestionar_profesor.php`**: Página para editar los datos de un miembro del personal y asignarlo al período escolar activo.
- **`public/js/admin_profesores.js`**: Archivo de lógica de cliente que, aunque presente, no parece estar en uso por las páginas anteriores.

---

### 1. `profesores_registro.php` (Registro y Listado General)

Esta página funciona como un panel de control principal para todo el personal.

#### Doble Funcionalidad

1.  **Formulario de Registro:** Una sección permite agregar un nuevo miembro del personal (profesor, administrativo, etc.) con sus datos básicos (nombre, cédula, email, categoría). En este paso inicial, el personal aún no está vinculado a un período escolar.
2.  **Lista Maestra:** La segunda sección muestra una lista de *todo* el personal registrado en el sistema. Gracias a una consulta `LEFT JOIN` en el backend, la lista indica claramente si cada persona está asignada o no al período escolar actualmente activo.

#### Flujo de Usuario y Componentes Técnicos

*   **Flujo:** El administrador primero registra a un nuevo miembro. Una vez registrado, este aparece en la "Lista de Personal Registrado". Al lado de cada nombre, un enlace **"Gestionar"** redirige al administrador a la página `gestionar_profesor.php`, pasando el ID del miembro del personal como parámetro en la URL.
*   **Lógica de Backend:** El script PHP maneja el registro directamente. Antes de insertar un nuevo registro, comprueba si la cédula ya existe para prevenir duplicados.
*   **JavaScript:** No se utiliza JavaScript externo; la página se basa en la recarga del servidor tras el envío del formulario y en enlaces de hipertexto estándar.

---

### 2. `gestionar_profesor.php` (Edición y Asignación a Período)

Esta página se dedica a la gestión detallada de un único miembro del personal.

#### Funcionalidad

1.  **Edición de Datos:** Permite la modificación de los datos básicos del individuo (nombre, cédula, teléfono, email, categoría).
2.  **Asignación al Período Activo:** La funcionalidad clave reside aquí. Un formulario permite:
    *   **Vincular** al personal con el período escolar activo.
    *   Especificar su `posición` (ej. "Grade 5 Teacher", "Director") y su rol de `homeroom_teacher` (si aplica).
    *   **Desvincular** al personal del período activo.

#### Flujo de Usuario y Componentes Técnicos

*   **Flujo:** El administrador llega a esta página tras hacer clic en "Gestionar". Modifica los datos o cambia el estado de la asignación y guarda los cambios.
*   **Lógica de Backend:** La página procesa sus propios datos. Al recibir un `POST`, el script PHP ejecuta dos acciones principales:
    1.  Un `UPDATE` en la tabla `profesores` para guardar los datos básicos.
    2.  Gestiona la tabla `profesor_periodo`:
        *   Si el checkbox "Asignar a este período" está marcado, realiza un `INSERT` (si no existía la asignación) o un `UPDATE` (si ya existía y se cambió la posición).
        *   Si el checkbox no está marcado, realiza un `DELETE` para quitar la asignación del período.
*   **JavaScript:** Utiliza un pequeño script **integrado (inline)** para mejorar la usabilidad, mostrando u ocultando los campos de `posición` y `homeroom` en función de si el checkbox de asignación está activo. No realiza llamadas a API.

---

### 3. `admin_profesores.js` (Archivo JavaScript No Vinculado)

Durante el análisis, se detectó que el archivo `public/js/admin_profesores.js` **no está siendo utilizado por las dos páginas PHP analizadas**.

*   **Funcionalidad Descrita en el JS:** El código en este archivo está diseñado para una interfaz de usuario más dinámica y avanzada, basada en AJAX, que permitiría:
    *   Seleccionar un período escolar y cargar dinámicamente una tabla con el personal asignado.
    *   Realizar **edición en línea** (inline editing) de la posición y el homeroom directamente sobre la tabla, sin recargar la página.
    *   Mostrar un formulario para asignar personal que aún no forma parte del período seleccionado.
*   **APIs Requeridas por el JS:** Este script depende de una serie de endpoints en la carpeta `/api/` que no son llamados por las páginas actuales:
    *   `GET /api/obtener_profesores.php`
    *   `GET /api/obtener_profesores_no_asignados.php`
    *   `POST /api/asignar_profesor.php`
    *   `POST actualizar_profesores.php`

### Conclusión General

El flujo de trabajo del Módulo Staff es funcional y se basa en un modelo clásico de PHP con recarga de página. Es robusto y claro. Sin embargo, la presencia del archivo `admin_profesores.js` sugiere que podría haber planes para una refactorización futura hacia una interfaz de usuario más moderna (Single Page Application-like) o que es un remanente de una versión anterior.

La funcionalidad de **eliminar personal** no se encuentra en los archivos analizados, lo que indica que debe ser manejada por un componente separado, probablemente `eliminar_profesor.php`.