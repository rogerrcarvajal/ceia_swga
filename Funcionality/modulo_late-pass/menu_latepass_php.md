# Documentación del Archivo: `pages/menu_latepass.php`

## 1. Propósito del Archivo

Este archivo PHP sirve como el **menú principal y centro de control** para todas las funcionalidades relacionadas con el control de acceso al plantel y la gestión de pases de llegada tarde (Late-Pass). Agrupa herramientas para el registro de movimientos de estudiantes, personal y vehículos, así como la consulta de estos registros y la generación de códigos QR.

---

## 2. Lógica de Negocio y Flujo de Carga (PHP)

La lógica del lado del servidor de esta página es mínima y se enfoca en la seguridad y el contexto:

1.  **Control de Acceso**: Verifica que el usuario esté autenticado y que su rol sea `admin`, `master` o `consulta`. Esto permite que personal de seguridad o administrativo con rol de consulta pueda acceder a estas herramientas.
2.  **Obtención del Período Activo**: Consulta la base de datos para obtener y mostrar el nombre del período escolar activo, proporcionando un contexto temporal relevante para las operaciones de control de acceso.

---

## 3. Estructura de la Interfaz y Opciones

La página presenta un menú de opciones claro y directo, cada una con un icono y una breve descripción:

1.  **Generar Códigos QR**
    *   **Enlace**: `pages/generar_qr.php`
    *   **Descripción**: "Permite la selección de un estudiante, staff o vehículo para generar su código QR."
    *   **Funcionalidad**: Punto de entrada para la creación de identificaciones con códigos QR.

2.  **Control de Acceso (Late-Pass)**
    *   **Enlace**: `pages/control_acceso.php`
    *   **Descripción**: "Escanea el código QR del estudiante, staff o vehículo autorizado para registrar su llegada."
    *   **Funcionalidad**: La interfaz principal para el registro en tiempo real de entradas y salidas.

3.  **Gestión y consulta de Late-Pass**
    *   **Enlace**: `pages/gestion_latepass.php`
    *   **Descripción**: "Consulta histórica de entradas tarde por estudiante y grado."
    *   **Funcionalidad**: Permite revisar los registros de llegadas tarde de estudiantes.

4.  **Gestión y consulta de Entrada/Salida Staff**
    *   **Enlace**: `pages/gestion_es_staff.php`
    *   **Descripción**: "Consulta los movimientos del personal por fecha y hora."
    *   **Funcionalidad**: Proporciona una herramienta para auditar los movimientos de entrada y salida del personal.

5.  **Gestión y consulta de Entrada/Salida Vehículos**
    *   **Enlace**: `pages/gestion_vehiculos.php`
    *   **Descripción**: "Consulta los movimientos de vehículos autorizados, hora de entrada y salida del colegio."
    *   **Funcionalidad**: Permite el seguimiento de los vehículos que ingresan y salen del plantel.
