# Análisis de Funcionalidad: Módulo Late-Pass (Generación de QR)

Este documento describe el flujo de trabajo y los componentes técnicos involucrados en la generación de códigos QR para estudiantes, personal (staff) y vehículos.

### Componentes Principales

- **`pages/generar_qr.php`**: Interfaz central para seleccionar la entidad a la que se le generará un QR.
- **Scripts Generadores de PDF**:
    - `src/reports_generators/generar_qr_pdf.php` (para Estudiantes)
    - `src/reports_generators/generar_qr_staff_pdf.php` (para Personal)
    - `src/reports_generators/generar_qr_vehiculo_pdf.php` (para Vehículos)

---

### 1. `pages/generar_qr.php` (Interfaz de Selección)

Esta página actúa como un centro de control o "despachador" que no genera el QR directamente, sino que prepara la solicitud para el script correcto.

#### Lógica de Backend (PHP)

1.  Obtiene el período escolar activo para asegurar que solo se listen entidades activas.
2.  Consulta la base de datos y obtiene listas separadas de:
    - Estudiantes activos.
    - Vehículos autorizados.
    - Personal (Staff) activo, convenientemente agrupado por categorías (Administrativo, Docente, etc.).
3.  Incrusta estos listados como un objeto JSON dentro del código HTML, para que estén disponibles para el script de frontend.

#### Lógica de Frontend (JavaScript `inline`)

1.  El usuario selecciona una categoría del menú lateral (ej. "Estudiantes").
2.  El script de JavaScript intercepta la selección.
3.  Puebla dinámicamente el menú desplegable (`<select>`) con los datos correspondientes a la categoría elegida.
4.  **Paso Clave:** Modifica el atributo `action` del formulario para que apunte al script generador de PDF correcto según la categoría seleccionada.
5.  Cuando el usuario hace clic en "Generar PDF con QR", el formulario envía el ID del ítem seleccionado al script generador apropiado.

---

### 2. Scripts Generadores de PDF (Ej. `generar_qr_pdf.php`)

Estos scripts reciben la solicitud del formulario y realizan el trabajo de generación.

#### Lógica de Backend (PHP)

1.  Recibe el `id` de la entidad (ej. estudiante) a través de `$_GET['id']`.
2.  Busca los detalles de esa entidad en la base de datos (ej. nombre y apellido).
3.  **Define el Contenido del QR:** Construye el string que se codificará. Este string contiene un **prefijo** que identifica el tipo de entidad, seguido del ID. Este formato es fundamental para el sistema de escaneo.
    - **`EST-123`** para un estudiante.
    - **`STF-45`** para un miembro del personal.
    - **`VEH-78`** para un vehículo.
4.  Utiliza la librería `endroid/qrcode` para generar una imagen PNG del código QR.
5.  Utiliza la librería `FPDF` para crear un nuevo documento PDF.
6.  Diseña un carnet de identificación simple en el PDF, insertando el nombre de la persona/vehículo y la imagen del código QR.
7.  Envía el PDF finalizado al navegador del usuario para su visualización o descarga.

### Conclusión

El sistema de generación de QR está bien estructurado, separando la interfaz de usuario de la lógica de generación. El uso de prefijos en el contenido del QR es una decisión de diseño inteligente que simplifica enormemente el posterior procesamiento en el módulo de control de acceso.
