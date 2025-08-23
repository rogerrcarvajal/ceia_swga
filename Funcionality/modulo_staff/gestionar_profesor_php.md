# Documentación del Archivo: `pages/gestionar_profesor.php`

## 1. Propósito del Archivo

Este archivo es la interfaz de **gestión individual** para un miembro del staff. Su propósito es doble: permite a un administrador **editar los datos personales** de un profesor o miembro del personal (nombre, cédula, categoría, etc.) y, al mismo tiempo, **gestionar su asignación** (matricular, desmatricular, o cambiar su cargo) dentro del período escolar activo.

Combina la lógica de visualización (GET) y de procesamiento (POST) en un solo archivo.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Carga Inicial de la Página (Método GET)

1.  **Recepción de ID**: El script obtiene el `id` del miembro del staff desde la URL.
2.  **Obtención de Datos**: Para construir la página, realiza varias consultas a la base de datos:
    *   Obtiene todos los datos del miembro del staff de la tabla `profesores`.
    *   Identifica el período escolar activo.
    *   Busca en la tabla `profesor_periodo` si existe una asignación para este profesor en el período activo.
3.  **Poblado del Formulario**: Utiliza los datos recuperados para rellenar todos los campos del formulario. Si se encontró una asignación, marca la casilla "Asignar a este período escolar" y selecciona la posición y el homeroom correspondientes.

### b. Procesamiento de Cambios (Método POST)

Cuando el administrador hace clic en "Guardar Cambios", el script ejecuta un proceso de actualización en dos fases:

1.  **Fase 1: Actualización de Datos Personales**
    *   El script primero ejecuta una consulta `UPDATE` sobre la tabla `profesores` para guardar cualquier cambio realizado en los campos de datos personales (nombre, cédula, email, etc.). Esta operación se realiza siempre que se envía el formulario.

2.  **Fase 2: Gestión de la Asignación**
    *   A continuación, el script revisa el estado de la casilla de verificación `asignar_periodo`.
    *   **Si está marcada**: Realiza una lógica de "actualizar o insertar" (upsert). Comprueba si ya existía un registro en `profesor_periodo`. Si existía, lo **actualiza** (`UPDATE`) con la nueva posición y homeroom. Si no existía, **inserta** (`INSERT`) un nuevo registro para matricular al profesor en el período.
    *   **Si no está marcada**: El script interpreta que el usuario desea desmatricular al profesor. Si existía un registro de asignación, lo **elimina** (`DELETE`) de la tabla `profesor_periodo`.

3.  **Feedback y Recarga de Datos**: Después de realizar las operaciones, se genera un mensaje de estado y se vuelven a consultar los datos de la asignación para que el formulario refleje inmediatamente los cambios guardados.

---

## 3. Estructura de la Interfaz

El formulario está dividido en dos secciones claras mediante el uso de `<fieldset>`:

*   **Datos Registrados**: Contiene los campos para la información personal del miembro del staff, que se corresponde con la tabla `profesores`.
*   **Asignar al Período**: Contiene el checkbox y los menús desplegables para gestionar la matrícula del staff en el período activo, correspondiéndose con la tabla `profesor_periodo`.

### JavaScript Embebido

Un pequeño script se encarga de la usabilidad, mostrando u ocultando la sección de detalles de la asignación (posición y homeroom) dependiendo de si la casilla "Asignar a este período escolar" está marcada o no.
