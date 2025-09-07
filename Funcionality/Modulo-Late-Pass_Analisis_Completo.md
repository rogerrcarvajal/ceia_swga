# Análisis Completo de Funcionalidad: Módulo Late-Pass

Este documento consolida el análisis exhaustivo de todos los componentes del Módulo Late-Pass, desde la generación de códigos QR hasta la consulta de registros. El módulo es una pieza central del sistema, caracterizado por una arquitectura consistente y una clara separación de responsabilidades.

---

## Parte 1: Generación de Códigos QR

Esta sección describe el flujo de trabajo para la creación de identificadores únicos para cada entidad del sistema.

### Componentes Principales

- **`pages/generar_qr.php`**: Interfaz central para seleccionar la entidad a la que se le generará un QR.
- **Scripts Generadores de PDF**:
    - `src/reports_generators/generar_qr_pdf.php` (para Estudiantes)
    - `src/reports_generators/generar_qr_staff_pdf.php` (para Personal)
    - `src/reports_generators/generar_qr_vehiculo_pdf.php` (para Vehículos)

### Flujo de Trabajo

1.  **Selección**: El administrador utiliza la interfaz de `generar_qr.php` para seleccionar una categoría (ej. "Estudiantes") y luego un individuo específico de una lista poblada dinámicamente.
2.  **Enrutamiento**: El JavaScript de la página asigna el `action` del formulario al script generador de PDF correcto según la categoría.
3.  **Generación**: El script PHP correspondiente recibe el ID, construye un string con un prefijo identificador (`EST-`, `STF-`, `VEH-`), genera una imagen de código QR con la librería `endroid/qrcode`, y la incrusta en un carnet de identificación simple usando `FPDF`.
4.  **Descarga**: El PDF resultante se envía al navegador para su descarga.

### Conclusión (Parte 1)

El sistema de generación es eficiente y robusto. El uso de prefijos en el contenido del QR es una decisión de diseño clave que simplifica drásticamente el procesamiento en el punto de escaneo.

---

## Parte 2: Control de Acceso (Escaneo de QR)

Esta es la funcionalidad principal del módulo, donde los códigos QR se utilizan para registrar movimientos en tiempo real.

### Componentes Principales

- **`pages/control_acceso.php`**: Interfaz de usuario para el escaneo.
- **`public/js/control_acceso.js`**: Lógica de cliente que procesa el escaneo.
- **APIs de Registro**: `registrar_llegada.php` (Estudiantes), `registrar_movimiento_staff.php` (Personal), `registrar_movimiento_vehiculo.php` (Vehículos).

### Flujo de Trabajo

1.  **Captura**: La página `control_acceso.php` mantiene el foco en un campo de texto, esperando la entrada de un lector de QR de hardware.
2.  **Procesamiento (JS)**: El script `control_acceso.js` captura la entrada, identifica el prefijo del código y determina a qué API debe enviar la solicitud.
3.  **Lógica de Negocio (API)**: Cada API de registro ejecuta reglas de negocio específicas:
    - **Estudiantes**: Siempre registra la llegada. Si es después de las 08:06:00, cuenta un "strike" de tardanza para la semana.
    - **Personal**: Gestiona un ciclo de trabajo diario (una entrada antes de las 12 PM, una salida después de las 12 PM).
    - **Vehículos**: Gestiona un ciclo simple de entrada/salida.
4.  **Feedback**: La API devuelve una respuesta JSON que el JavaScript utiliza para mostrar una tarjeta de información con un código de colores, informando al operador del resultado del escaneo.

### Conclusión (Parte 2)

El sistema de control de acceso es el núcleo funcional del módulo. Está diseñado para ser rápido y aplica reglas de negocio complejas y bien diferenciadas para cada tipo de entidad, garantizando la integridad de los datos.

---

## Parte 3, 4 y 5: Gestión y Consulta de Registros

Las tres secciones de consulta (Late-Pass de Estudiantes, Entradas/Salidas de Staff y Movimientos de Vehículos) siguen un patrón de diseño idéntico y consistente, lo que representa una de las mayores fortalezas del módulo.

### Componentes Comunes

- **Páginas de Interfaz**: `gestion_latepass.php`, `gestion_es_staff.php`, `gestion_vehiculos.php`.
- **Scripts de Lógica**: `gestion_latepass.js`, `gestion_es_staff.js`, `gestion_vehiculos.js`.
- **APIs de Consulta**: `consultar_latepass.php`, `consulta_movimientos_staff.php`, `consulta_movimientos_vehiculos.php`.

### Flujo de Trabajo Común

1.  **Interfaz**: Cada página ofrece un conjunto de filtros (siempre por semana, y luego por estudiante, personal o vehículo).
2.  **Lógica de Cliente (JS)**: Al cargar la página, el script establece la semana actual por defecto y carga los datos iniciales. Cada vez que un filtro cambia, se realiza una nueva petición `fetch` a la API correspondiente.
3.  **API de Datos**: La API recibe los filtros, construye una consulta SQL para obtener los datos relevantes de la base de datos y los devuelve en formato JSON.
4.  **Visualización**: El script de JavaScript procesa la respuesta JSON y actualiza dinámicamente el contenido de la tabla HTML sin necesidad de recargar la página.
5.  **Exportación a PDF**: Un botón permite abrir una nueva pestaña que apunta a un script generador de PDF, pasándole los mismos filtros para que el reporte impreso coincida con la vista en pantalla.

### Conclusión (Parte 3, 4 y 5)

Estos módulos de consulta están muy bien diseñados para la inteligencia de negocio. Permiten a los administradores filtrar y visualizar datos de manera eficiente. La consistencia en el diseño y la arquitectura facilita enormemente el mantenimiento y la escalabilidad del sistema.

---

## Conclusión Final del Módulo Late-Pass

El Módulo Late-Pass es una pieza de ingeniería de software sólida, bien planificada y ejecutada.

- **Fortalezas Clave**:
    - **Arquitectura Consistente**: El uso repetido del patrón (Interfaz de Filtros -> JS -> API -> Tabla Dinámica) en todas las secciones de consulta es ejemplar.
    - **Separación de Responsabilidades**: La división entre presentación (HTML), interacción (JS) y lógica (PHP/API) es clara y sigue las mejores prácticas.
    - **Experiencia de Usuario Fluida**: Las interfaces son dinámicas, responden rápidamente a las acciones del usuario y proporcionan feedback visual claro.
    - **Lógica de Negocio Robusta**: Las reglas para el registro de movimientos son específicas y están protegidas por transacciones de base de datos.

- **Punto Menor de Mejora Sugerido**:
    - Las APIs de consulta para Staff y Vehículos podrían mejorarse para manejar la opción de "Todos", que actualmente se ofrece en la interfaz pero no está implementada en el backend. Habilitar esta funcionalidad proporcionaría una visión general valiosa para los administradores.
