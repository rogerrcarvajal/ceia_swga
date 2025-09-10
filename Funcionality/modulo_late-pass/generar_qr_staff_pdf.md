# Documentación del Archivo: `src/reports_generators/generar_qr_staff_pdf.php`

## 1. Propósito del Archivo

Este script de backend se encarga de generar un **documento PDF con el código QR de un miembro del staff**. Es invocado desde la página `pages/generar_qr.php` y produce un carnet o identificación imprimible para el personal.

---

## 2. Lógica de Negocio y Flujo de Operación

El flujo es muy similar al de `generar_qr_pdf.php` (para estudiantes), con adaptaciones para el personal:

1.  **Verificación de Seguridad**: Asegura que el usuario esté autenticado.
2.  **Inclusión de Librerías**: Carga `config.php`, `fpdf.php` y `qrlib.php`.
3.  **Obtención de Datos**: 
    *   Recibe el `id` del profesor/staff a través de un parámetro `GET`.
    *   Consulta la tabla `profesores` para obtener el nombre completo del staff.
    *   Consulta la tabla `profesor_periodo` para obtener la `posicion` del staff en el período activo.
4.  **Generación del Código QR**: 
    *   Utiliza `QRcode::png()` para crear una imagen temporal del QR.
    *   El contenido del QR es el ID del staff precedido por el prefijo `STF-` (ej. `STF-P001`).
5.  **Creación del PDF (FPDF)**: 
    *   Define una clase `PDFStaff` que extiende `FPDF`, personalizando el `Header` y `Footer`.
    *   Añade una página al PDF.
    *   Establece un título ("CARNET QR STAFF / PROFESOR").
    *   Muestra los datos del staff (nombre completo y posición) utilizando un método `Section`.
    *   **Incrustación del QR**: Inserta la imagen temporal del código QR en el PDF.
6.  **Salida y Limpieza**: 
    *   Envía el PDF generado directamente al navegador (`Output('D')`), forzando la descarga.
    *   Elimina el archivo de imagen temporal del QR.

---

## 3. Librerías Utilizadas

*   **FPDF**: Para la creación y manipulación de documentos PDF.
*   **phpqrcode**: Para la generación de imágenes de códigos QR.
