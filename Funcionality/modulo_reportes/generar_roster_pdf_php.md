# Documentación del Archivo: `src/reports_generators/generar_roster_pdf.php`

## 1. Propósito del Archivo

Este script de backend es el responsable de generar un **documento PDF completo del Roster** de la institución para el período escolar activo. Es la versión imprimible y formal del reporte HTML generado por `roster_actual.php`, incluyendo tanto el personal como los estudiantes organizados por grado, y estadísticas de resumen.

---

## 2. Lógica de Negocio y Flujo de Operación

La lógica de este script es idéntica a la de `roster_actual.php` en cuanto a la obtención y procesamiento de datos, pero su salida es un PDF.

1.  **Verificación de Seguridad**: Asegura que el usuario esté autenticado.
2.  **Inclusión de Librerías**: Carga `config.php` y `fpdf.php`.
3.  **Obtención y Procesamiento de Datos**: Este script **duplica completamente la lógica de obtención y procesamiento de datos** de `roster_actual.php`. Esto incluye:
    *   Obtener el período activo.
    *   Consultar y categorizar al personal administrativo y docente (incluyendo la lógica de `$mapa_areas` y `$orden_admin`).
    *   Asociar maestros de `homeroom` con sus grados.
    *   Consultar y agrupar a los estudiantes por grado.
    *   Calcular estadísticas de resumen (total de estudiantes staff, regulares, por nivel educativo).
4.  **Generación del PDF (FPDF)**: 
    *   Define una clase `PDF_Roster` que extiende `FPDF`, personalizando el `Header` (logo, título del colegio, período activo) y el `Footer` (línea de color, dirección, paginación).
    *   Añade una página al PDF y establece un título principal dinámico (ej. "Roster [Nombre del Período]").
    *   **Renderizado de Tablas de Personal**: Dibuja tablas para el personal administrativo y para cada categoría de personal docente, con sus respectivas posiciones y nombres.
    *   **Renderizado de Listas de Estudiantes**: Para cada grado, dibuja un título con el nombre del grado y el número de estudiantes, el nombre del profesor de `homeroom`, y luego lista a cada estudiante.
    *   **Tabla de Resumen**: Al final del documento, añade una tabla de resumen con las estadísticas de estudiantes (Staff, Regulares, Total) y la distribución por niveles (Daycare/Preschool/K, Elementary, Secondary).
5.  **Salida del Documento**: Envía el PDF generado directamente al navegador con el método `$pdf->Output('D', ...)` lo que fuerza la descarga del archivo con un nombre descriptivo (ej. `Roster_Periodo_2024-2025.pdf`).

---

## 3. Observación sobre Código Duplicado

La duplicación de la lógica de obtención y procesamiento de datos entre `roster_actual.php` (HTML) y `generar_roster_pdf.php` (PDF) es un punto significativo para una futura refactorización. Idealmente, esta lógica debería residir en una función o clase compartida que ambos generadores de reportes puedan invocar, mejorando la mantenibilidad y reduciendo la posibilidad de inconsistencias.
