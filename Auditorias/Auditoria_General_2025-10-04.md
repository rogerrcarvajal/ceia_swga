# Informe de Auditoría General de SWGA - 04 de Octubre de 2025

## 1. Resumen Ejecutivo

Se ha realizado una auditoría exhaustiva de los módulos de **Estudiantes** y **Late-Pass**. El análisis revela una plataforma con una **arquitectura de software sólida y consistente** y una lógica de negocio bien implementada en el frontend. Sin embargo, se han identificado **vulnerabilidades de seguridad críticas** en el backend debido a la ausencia de controles de acceso en múltiples APIs y scripts de generación de reportes.

- **Fortalezas Notables:**
    - Arquitectura consistente y reutilizable (especialmente en el módulo Late-Pass).
    - Lógica de negocio robusta y bien pensada (transacciones, manejo de estados).
    - Excelente uso de sentencias preparadas para prevenir inyección SQL.
    - Experiencia de usuario fluida gracias al uso de JavaScript asíncrono.

- **Debilidades Críticas:**
    - **Falta de Autenticación en Endpoints Clave:** Múltiples archivos de API y generadores de PDF no verifican la sesión del usuario, permitiendo el acceso y la manipulación no autorizada de datos sensibles.
    - **Inconsistencias Lógicas:** Se encontró una discrepancia mayor en la lógica de "strikes" por tardanza de los estudiantes.

A continuación, se detallan los hallazgos y recomendaciones para cada módulo.

---

## 2. Módulo de Estudiantes

### 2.1. Coherencia y Funcionalidad
- **Estado:** **Excelente.**
- **Observación:** La implementación de todas las funcionalidades (inscripción, gestión de expedientes, asignación a períodos y autorización de salida) es altamente consistente con la documentación. El flujo de trabajo es lógico y robusto.

### 2.2. Vulnerabilidades de Seguridad
- **Severidad:** **Crítica.**
- **Hallazgo:** Se identificó una falta de control de acceso (verificación de `$_SESSION['usuario']`) en los siguientes archivos de API:
    - `api/obtener_estudiante.php`
    - `api/obtener_padre.php`
    - `api/obtener_madre.php`
    - `api/obtener_ficha_medica.php`
    - `api/obtener_estudiantes_por_periodo.php`
    - `api/obtener_estudiantes_no_asignados.php`
- **Impacto:** Un atacante podría leer información sensible de estudiantes, padres y fichas médicas sin autenticación, simplemente conociendo la URL y un ID válido.
- **Recomendación:** **Añadir el bloque de verificación de sesión al inicio de cada uno de estos archivos.** El bloque ya presente en `api/asignar_estudiante.php` puede ser usado como modelo.

- **Severidad:** **Media.**
- **Hallazgo:** El generador de PDFs `src/reports_generators/generar_pdf_salida.php` no verifica la sesión del usuario.
- **Impacto:** Permite la visualización no autorizada de los detalles de una autorización de salida.
- **Recomendación:** Añadir el bloque de verificación de sesión al inicio del script.

### 2.3. Oportunidades de Mejora
- **Código Duplicado:** Las APIs `obtener_padre.php` y `obtener_madre.php` son casi idénticas. Se podrían refactorizar en una función genérica para reducir la duplicación.
- **Consistencia en Respuestas:** Las APIs de obtención de datos tienen formatos de respuesta JSON inconsistentes. Estandarizar a un formato único (ej. `{'status': '...', 'data': ..., 'message': '...'}`) mejoraría la predictibilidad.
- **Optimización de Frontend:** Las llamadas `fetch` para obtener datos de padre, madre y ficha médica en `admin_estudiantes.js` podrían ejecutarse en paralelo (`Promise.all`) para una carga ligeramente más rápida.

---

## 3. Módulo de Late-Pass

### 3.1. Coherencia y Funcionalidad
- **Estado:** **Bueno.**
- **Observación:** La arquitectura es ejemplar y consistente con la documentación. Sin embargo, hay una discrepancia lógica importante.
- **Discrepancia:** La lógica para contar "strikes" por tardanza está **desactivada** en `api/registrar_llegada.php`, pero la interfaz de consulta (`gestion_latepass.php`) sí calcula y muestra los strikes. Esto crea una inconsistencia: el sistema reporta los strikes pero no actúa sobre ellos en el momento del registro.
- **Recomendación:** Reactivar y completar la lógica de negocio para el conteo y manejo de strikes en `api/registrar_llegada.php` para que sea consistente con el resto del módulo.

### 3.2. Vulnerabilidades de Seguridad
- **Severidad:** **Crítica.**
- **Hallazgo:** Faltan controles de acceso en los siguientes archivos de API de registro:
    - `api/registrar_llegada.php` (Estudiantes)
    - `api/registrar_movimiento_staff.php` (Personal)
- **Impacto:** Permite a un actor no autenticado registrar llegadas y movimientos para estudiantes y personal, comprometiendo la integridad de los registros de asistencia.
- **Recomendación:** **Añadir el bloque de verificación de sesión al inicio de ambos archivos.** El script `api/registrar_movimiento_vehiculo.php` ya lo implementa correctamente y puede usarse como modelo.

- **Severidad:** **Media.**
- **Hallazgo:** El generador de PDFs `src/reports_generators/generar_latepass_pdf.php` no verifica la sesión del usuario.
- **Impacto:** Permite la visualización no autorizada de los reportes de llegadas tarde.
- **Recomendación:** Añadir un bloque de verificación de sesión al inicio del script.

### 3.3. Oportunidades de Mejora
- **Lógica Incompleta:** La documentación menciona que el filtro "Todos" en las consultas de Staff y Vehículos no está implementado en el backend. Implementar esta lógica proporcionaría una funcionalidad de reporte más completa.
- **Supresión de Errores:** Eliminar `error_reporting(0)` de los scripts de registro y confiar en una configuración global de manejo de errores para un mejor diagnóstico de problemas.

## 4. Conclusión General

El sistema SWGA tiene una base de código y una arquitectura muy sólidas. Las vulnerabilidades identificadas, aunque críticas, son de una naturaleza similar (falta de control de acceso) y pueden ser corregidas de manera sistemática aplicando un patrón de seguridad consistente (el bloque de verificación de sesión) en todos los endpoints desprotegidos.

La prioridad debe ser **solucionar las vulnerabilidades de control de acceso** para proteger los datos y la integridad del sistema. En segundo lugar, se debe abordar la inconsistencia en la lógica de "strikes" para asegurar que el módulo Late-Pass funcione como un todo coherente.
