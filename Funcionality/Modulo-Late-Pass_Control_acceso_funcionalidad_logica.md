# Análisis de Funcionalidad: Módulo Late-Pass (Control de Acceso)

Este documento describe el flujo de trabajo y los componentes técnicos del sistema de escaneo de códigos QR para el registro de entradas y salidas.

### Componentes Principales

- **`pages/control_acceso.php`**: Interfaz de usuario para el escaneo.
- **`public/js/control_acceso.js`**: Lógica de cliente que procesa el escaneo y se comunica con el backend.
- **APIs de Registro**:
    - `api/registrar_llegada.php` (Estudiantes)
    - `api/registrar_movimiento_staff.php` (Personal)
    - `api/registrar_movimiento_vehiculo.php` (Vehículos)

---

### 1. `pages/control_acceso.php` (Interfaz de Escaneo)

Es una página minimalista diseñada para ser usada con un lector de códigos QR de hardware (ej. tipo pistola USB).

- **Componente Central:** Un campo de texto (`<input type="text" id="qr-input">`) con `autofocus`. El lector de QR "escribe" el contenido del código en este campo y simula un "Enter".
- **Feedback al Usuario:** Un `div` (`#qr-result`) se usa para mostrar mensajes de éxito o error tras cada escaneo.

---

### 2. `public/js/control_acceso.js` (Lógica de Cliente)

Este script es el cerebro del frontend y orquesta todo el proceso de escaneo.

#### Flujo de Trabajo

1.  **Captura:** El script espera un evento `change` o `keypress` ("Enter") en el campo de texto.
2.  **Procesamiento (`procesarCodigo`):**
    - **Normalización:** Limpia el código escaneado (quita espacios, convierte a mayúsculas).
    - **Identificación:** Revisa el **prefijo** del código (`EST-`, `STF-`, `VEH-`).
    - **Enrutamiento:** Basado en el prefijo, determina a cuál de las tres APIs de registro debe enviar la solicitud. Si el prefijo es inválido, muestra un error localmente.
    - **Llamada a API:** Envía el código a la API correspondiente usando una petición `fetch` de tipo `POST`.
3.  **Visualización de Respuesta:**
    - Recibe la respuesta JSON de la API.
    - Llama a la función `mostrarMensaje` que formatea una tarjeta de información detallada.
    - La tarjeta tiene un código de color distintivo para cada tipo de entidad (azul para estudiantes, marrón para staff, verde para vehículos) para una rápida identificación visual.
    - El mensaje de resultado desaparece tras 5 segundos, y el sistema se prepara para el siguiente escaneo.

---

### 3. APIs de Registro (Lógica de Backend)

Cada API maneja la lógica de negocio específica para su tipo de entidad, pero todas siguen un patrón común.

#### Patrón Común

1.  Reciben el código vía `POST`.
2.  Validan el prefijo y extraen el ID numérico.
3.  Obtienen la fecha/hora actual del servidor.
4.  Buscan los detalles de la entidad en la base de datos.
5.  Usan **transacciones de base de datos** (`beginTransaction`, `commit`, `rollBack`) para garantizar que las operaciones de escritura sean atómicas y seguras.
6.  Devuelven una respuesta JSON estandarizada (`{success, message, data}`).

#### Lógica de Negocio Específica

- **`registrar_llegada.php` (Estudiantes):**
    - Previene registros duplicados para el mismo día.
    - **Siempre** inserta un registro en `llegadas_tarde`.
    - Si la hora de llegada es posterior a las `08:06:00`, la considera "tarde" y calcula el número de "strikes" (llegadas tarde) que el estudiante acumula en la semana.

- **`registrar_movimiento_staff.php` (Personal):**
    - Implementa una lógica de ciclo de trabajo diario:
    - **Primer escaneo del día:** Se registra como **Entrada**. Debe ser antes de las 12:00 PM.
    - **Segundo escaneo del día:** Se registra como **Salida**. Debe ser después de las 12:00 PM.
    - Escaneos posteriores en el mismo día son rechazados.

- **`registrar_movimiento_vehiculo.php` (Vehículos):**
    - Implementa una lógica de entrada/salida simple:
    - **Primer escaneo:** Se registra como **Entrada**, creando un nuevo registro en `registro_vehiculos`.
    - **Segundo escaneo:** Se registra como **Salida**, actualizando el registro de entrada previamente creado.

### Conclusión

El sistema de control de acceso es robusto y eficiente. La lógica está claramente dividida: el frontend se encarga de la captura y el enrutamiento, mientras que cada API de backend aplica reglas de negocio específicas y complejas para cada tipo de entidad, garantizando la integridad y coherencia de los datos registrados.
