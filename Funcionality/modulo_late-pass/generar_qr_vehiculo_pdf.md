# Documentación del Archivo: `src/reports_generators/generar_qr_vehiculo_pdf.php`

## 1. Propósito del Archivo

Este script de backend está diseñado para generar un **documento PDF con el código QR de un vehículo autorizado**. Es invocado desde la página `pages/generar_qr.php` y produce una identificación imprimible para los vehículos que ingresan al plantel.

---

## 2. Lógica de Negocio y Flujo de Operación

El flujo es muy similar a los generadores de QR para estudiantes y staff, con adaptaciones para vehículos:

1.  **Verificación de Seguridad**: Asegura que el usuario esté autenticado.
2.  **Inclusión de Librerías**: Carga `config.php`, `fpdf.php` y `qrlib.php`.
3.  **Obtención de Datos**: 
    *   Recibe el `id` del vehículo a través de un parámetro `GET`.
    *   Consulta la tabla `vehiculos` para obtener la placa y el modelo.
    *   Realiza un `JOIN` con la tabla `estudiantes` para obtener el nombre del estudiante asociado al vehículo (el propietario o principal usuario).
4.  **Generación del Código QR**: 
    *   Utiliza `QRcode::png()` para crear una imagen temporal del QR.
    *   El contenido del QR es el ID del vehículo precedido por el prefijo `VEH-` (ej. `VEH-V001`).
5.  **Creación del PDF (FPDF)**: 
    *   Define una clase `PDFVehiculo` que extiende `FPDF`, personalizando el `Header` y `Footer`.
    *   Añade una página al PDF.
    *   Establece un título ("CARNET QR VEHICULAR").
    *   Muestra los datos del vehículo (placa, modelo, propietario) utilizando un método `Section`.
    *   **Incrustación del QR**: Inserta la imagen temporal del código QR en el PDF.
6.  **Salida y Limpieza**: 
    *   Envía el PDF generado directamente al navegador (`Output('D')`), forzando la descarga.
    *   Elimina el archivo de imagen temporal del QR.

---

## 3. Librerías Utilizadas

*   **FPDF**: Para la creación y manipulación de documentos PDF.
*   **phpqrcode**: Para la generación de imágenes de códigos QR.
