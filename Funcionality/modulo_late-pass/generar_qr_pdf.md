# Documentación del Archivo: `src/reports_generators/generar_qr_pdf.php`

## 1. Propósito del Archivo

Este script de backend está diseñado para generar un **documento PDF con el código QR de un estudiante específico**. Es invocado desde la página `pages/generar_qr.php` y proporciona un carnet o identificación imprimible que puede ser utilizado en el sistema de control de acceso.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Verificación de Seguridad**: Asegura que el usuario esté autenticado.
2.  **Inclusión de Librerías**: Carga `config.php` (para la conexión a la BD), `fpdf.php` (para la generación de PDF) y `qrlib.php` (para la generación de códigos QR).
3.  **Obtención de Datos**: 
    *   Recibe el `id` del estudiante a través de un parámetro `GET` en la URL.
    *   Consulta la tabla `estudiantes` para obtener el nombre completo y apellido del estudiante.
    *   **Importante**: Realiza una segunda consulta a la tabla `estudiante_periodo` para obtener el `grado_cursado` del estudiante en el período activo. Esto asegura que el carnet muestre información relevante para el período actual.
4.  **Generación del Código QR**: 
    *   Utiliza la librería `phpqrcode` (`QRcode::png()`) para crear una imagen temporal del código QR.
    *   El contenido del QR es el ID del estudiante precedido por el prefijo `EST-` (ej. `EST-V12345678`). Este prefijo es crucial para que el sistema de control de acceso (`control_acceso.js`) identifique el tipo de entidad.
5.  **Creación del PDF (FPDF)**: 
    *   Define una clase `Generar_qr_pdf` que extiende `FPDF`, personalizando el `Header` (con el logo y el período activo) y el `Footer` (con información de contacto y número de página).
    *   Añade una página al PDF.
    *   Establece un título para el documento ("CONTROL DE ACCESO LATE-PASS").
    *   Muestra los datos del estudiante (nombre completo y grado) utilizando métodos personalizados (`DataRow`).
    *   **Incrustación del QR**: Inserta la imagen temporal del código QR en una posición específica del PDF.
6.  **Salida y Limpieza**: 
    *   Envía el PDF generado directamente al navegador con el método `Output('D')`, lo que fuerza la descarga del archivo con un nombre descriptivo.
    *   Elimina el archivo de imagen temporal del QR del servidor (`unlink($qr_temp_file)`).

---

## 3. Librerías Utilizadas

*   **FPDF**: Para la creación y manipulación de documentos PDF.
*   **phpqrcode**: Para la generación de imágenes de códigos QR.
