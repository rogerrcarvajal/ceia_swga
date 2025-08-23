# Documentación del Archivo: `src/reports_generators/generar_planilla_pdf.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar una **planilla de inscripción completa en formato PDF** para un estudiante específico. Recopila toda la información relevante del estudiante, sus padres y su ficha médica desde la base de datos y la presenta en un documento estructurado y listo para imprimir.

---

## 2. Lógica de Negocio y Flujo de Operación

1.  **Verificación de Seguridad**: El script asegura que el usuario esté autenticado antes de proceder.
2.  **Inclusión de Librerías**: Carga `config.php` (para la conexión a la BD) y `fpdf.php` (para la generación de PDF).
3.  **Recepción de Parámetro**: Obtiene el `id` del estudiante a través de un parámetro `GET` en la URL. Si el ID no es válido o no se proporciona, detiene la ejecución con un mensaje de error.
4.  **Obtención de Datos (Múltiples Consultas)**: Para construir la planilla completa, el script realiza varias consultas individuales a la base de datos:
    *   **Estudiante**: Consulta la tabla `estudiantes` para obtener todos los datos personales del estudiante.
    *   **Padre**: Si el estudiante tiene un `padre_id` asociado, consulta la tabla `padres` para obtener la información del padre.
    *   **Madre**: Si el estudiante tiene un `madre_id` asociado, consulta la tabla `madres` para obtener la información de la madre.
    *   **Ficha Médica**: Consulta la tabla `salud_estudiantil` para obtener los datos de salud del estudiante.
    *   **Asignación a Período**: Consulta la tabla `estudiante_periodo` para obtener el `grado_cursado` del estudiante en el período activo. Esto es importante para mostrar el grado actual en la planilla.
5.  **Generación del PDF (FPDF)**: 
    *   Define una clase `PlanillaPDF` que extiende `FPDF`, personalizando el `Header` (con el logo del colegio y el período activo) y el `Footer` (con información de contacto y número de página).
    *   Añade una página al PDF y establece un título principal ("PLANILLA DE INSCRIPCIÓN").
    *   Utiliza métodos personalizados (`SectionTitle`, `DataRow`) para organizar la información en secciones lógicas (Datos del Estudiante, Datos del Padre, Datos de la Madre, Ficha Médica).
    *   Maneja la visualización de datos booleanos (como `dislexia`) como "Sí" o "No" y proporciona "N/A" para campos vacíos.
    *   Incluye un bloque final para firmas y aceptación de términos.
6.  **Salida del Documento**: Envía el PDF generado directamente al navegador con el método `$pdf->Output('D', ...)` lo que fuerza la descarga del archivo con un nombre descriptivo (ej. `Planilla_Juan_Perez.pdf`).

---

## 3. Librerías Utilizadas

*   **FPDF**: Para la creación y manipulación de documentos PDF.
