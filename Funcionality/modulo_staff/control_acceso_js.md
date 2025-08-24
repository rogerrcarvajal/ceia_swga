# Documentación del Archivo: `public/js/control_acceso.js`

## 1. Propósito del Archivo

Este archivo JavaScript es el **cerebro y sistema nervioso central** de la página de Control de Acceso en tiempo real. Orquesta toda la lógica del lado del cliente, convirtiendo una simple caja de texto en una potente interfaz de registro de movimientos. Sus responsabilidades incluyen capturar la entrada de datos, determinar el tipo de entidad (estudiante, staff, vehículo), comunicarse con la API correcta y proporcionar un feedback visual claro e instantáneo al operador.

---

## 2. Lógica de Negocio y Flujo de Operación

### a. Inicialización y Listeners de Eventos

Al cargar la página, el script se prepara para la acción:

1.  **Asigna Listeners**: Escucha dos eventos en el campo de entrada `#qr-input`:
    *   `change`: Se activa cuando se escanea un código QR o se pega texto y se sale del campo.
    *   `keypress`: Se activa con cada tecla, pero específicamente busca la tecla "Enter" para procesar la entrada manual. Ambos eventos llaman a la función principal `procesarCodigo`.
2.  **Auto-Focus Constante**: Utiliza una función `setInterval` que se ejecuta cada medio segundo para comprobar si el foco del navegador está en el campo de entrada. Si no lo está, lo vuelve a enfocar. Esta es una característica de usabilidad crucial para un entorno de escaneo rápido, asegurando que el sistema siempre esté listo para el siguiente código sin necesidad de que el operador haga clic en el campo.

### b. Función Principal: `procesarCodigo(codigo)`

Esta función asíncrona se ejecuta cada vez que se introduce un código y es el corazón del flujo de trabajo:

1.  **Control de Concurrencia**: Utiliza una variable `procesando` como un semáforo o *flag*. Si ya se está procesando un código, la función no hace nada, evitando así registros duplicados por escaneos accidentales o múltiples pulsaciones de "Enter".
2.  **Normalización del Código**: Limpia y estandariza el código introducido (`trim()`, `toUpperCase()`, etc.) para asegurar consistencia.
3.  **Lógica de Enrutamiento por Prefijo**: Esta es la parte más inteligente del script. Inspecciona el inicio del código para decidir qué hacer:
    *   Si empieza con `"EST-"`, determina que es un estudiante y establece la URL de la API a `api/registrar_llegada.php`.
    *   Si empieza con `"STF-"`, es un miembro del staff y la URL se establece a `api/registrar_movimiento_staff.php`.
    *   Si empieza con `"VEH-"`, es un vehículo y la URL se establece a `api/registrar_movimiento_vehiculo.php`.
    *   Si el prefijo no coincide con ninguno, muestra un error de "QR no reconocido" y detiene el proceso.
4.  **Llamada a la API (Fetch)**: Envía el código normalizado y una marca de tiempo (`timestamp`) a la API correspondiente mediante una solicitud `POST`.
5.  **Manejo de Respuesta**: Procesa la respuesta JSON de la API. Si la respuesta es de éxito, pasa los datos a la función `mostrarMensaje`. Si es un error, pasa el mensaje de error.
6.  **Finalización y Reseteo**: En el bloque `finally` (que se ejecuta siempre, haya habido éxito o error), se resetea el flag `procesando` a `false` y se vuelve a enfocar el campo de entrada, dejando el sistema listo para el siguiente escaneo.

### c. Función de Feedback Visual: `mostrarMensaje(tipo, data)`

Esta función es responsable de toda la comunicación visual con el operador:

*   **Construcción Dinámica de HTML**: En lugar de un mensaje genérico, construye un bloque de HTML detallado y formateado basado en el tipo de entidad (`EST`, `STF`, `VEH`) que se procesó.
*   **Código de Colores**: Aplica un color de fondo diferente al `div` de resultados para cada tipo de entidad (azul para estudiantes, marrón para staff, verde para vehículos), permitiendo al operador identificar de un vistazo qué tipo de registro se procesó.
*   **Información Relevante**: Muestra los datos más importantes para la verificación, como el nombre completo, el grado o la posición, y la hora del registro.
*   **Mensajes Temporales**: Los mensajes de éxito o error se muestran en pantalla y desaparecen automáticamente después de 5 segundos.
