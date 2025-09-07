<?php
require_once __DIR__ . '/../src/lib/Parsedown.php';
$Parsedown = new Parsedown();
$page_title = "Documentación Técnica: Módulo Staff";

$markdown_content = <<<'MARKDOWN'
# Análisis de Funcionalidad: Módulo Staff

Este documento detalla el flujo de trabajo y la lógica de negocio exclusivos del Módulo de Staff, cuya responsabilidad es el registro y la gestión de la información del personal de la institución.

---

### Arquitectura y Propósito

El propósito de este módulo es manejar el ciclo de vida de los datos del personal (docentes, administrativos, etc.). Su arquitectura se basa en un modelo clásico de PHP, donde las acciones se procesan en el backend mediante la recarga de páginas.

**Nota Importante:** Funcionalidades como la generación de códigos QR, el registro de entradas/salidas y la creación de reportes de asistencia para el personal **no residen en este módulo**, sino en los módulos de **Late-Pass** y **Reportes**, respectivamente.

---

### 1. `profesores_registro.php` (Registro y Listado General)

Esta página funciona como el panel de control principal para la administración del personal.

#### Doble Funcionalidad

1.  **Formulario de Registro:** Proporciona una interfaz para crear un nuevo registro de personal, capturando sus datos básicos como nombre, cédula, teléfono, email y categoría (ej. "Staff Docente"). En este paso, el personal solo se crea en el sistema, pero aún no está vinculado a un período escolar.
2.  **Lista Maestra:** Muestra una lista de **todo** el personal registrado en la base de datos. Gracias a una consulta `LEFT JOIN`, la lista indica de forma clara si cada persona ya ha sido asignada al período escolar activo, proporcionando un resumen visual del estado de la plantilla.

#### Flujo de Trabajo

El flujo es directo: un administrador crea un nuevo registro de personal. Una vez creado, el miembro del personal aparece en la "Lista Maestra". Junto a su nombre, un enlace de **"Gestionar"** permite pasar a la siguiente etapa del ciclo de vida.

---

### 2. `gestionar_profesor.php` (Edición y Asignación a Período)

Esta página se dedica a la gestión detallada de un único miembro del personal, seleccionado desde la lista anterior.

#### Funcionalidad

1.  **Edición de Datos:** Permite modificar la información básica del individuo (nombre, cédula, etc.).
2.  **Asignación al Período Activo:** Esta es la funcionalidad clave del módulo. Un formulario permite vincular al miembro del personal con el período escolar activo, especificando su `posición` (ej. "Grade 5 Teacher", "Director") y su rol de `homeroom_teacher`, si aplica. También permite desvincularlo.

#### Lógica de Negocio

Al guardar los cambios, el script PHP actualiza los datos del profesor y gestiona su vínculo con el período escolar en la tabla `profesor_periodo`. Si se desmarca la casilla de asignación, el vínculo se elimina, pero el registro del profesor permanece en el sistema para futuras asignaciones.

---

### Conclusión General del Módulo

El Módulo Staff cumple de manera efectiva y robusta con sus dos responsabilidades principales: **registrar al personal y asignarlo a un período escolar**. Su lógica es clara y se centra exclusivamente en la gestión de los datos maestros del personal, dejando que otros módulos consuman esta información para sus propios fines.
MARKDOWN;

require_once __DIR__ . '/view_document.php';
