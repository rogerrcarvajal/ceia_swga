# Documentación del Archivo: `Funcionalidad_Modulo_Ayuda_Manua_Usuario.md`

## 1. Propósito del Archivo

Este archivo Markdown (`.md`) constituye el **Manual de Usuario principal** del sistema SWGA. Su propósito es proporcionar una guía de alto nivel y fácil comprensión para los administradores y usuarios del sistema, explicando las funcionalidades clave de cada módulo y cómo interactuar con ellas. Sirve como la primera capa de documentación accesible directamente desde la interfaz de usuario.

---

## 2. Estructura y Contenido

El manual está organizado por módulos principales del sistema, siguiendo una estructura lógica y jerárquica:

*   **Introducción**: Una bienvenida general al manual y al sistema.
*   **Módulo de Control de Acceso**: Describe la función de login, los roles de usuario y el dashboard como punto de entrada.
*   **Módulo de Estudiantes**: Detalla las funcionalidades de inscripción, gestión de expedientes y asignación a períodos.
*   **Módulo de Staff**: Explica el registro, gestión y control de entradas/salidas del personal.
*   **Módulo de Late-Pass**: Cubre la generación y gestión de pases de llegada tarde.
*   **Módulo de Reportes**: Describe la centralización de la exportación de información y los tipos de reportes disponibles.
*   **Módulo de Mantenimiento**: Detalla las herramientas críticas para la administración del sistema, como la gestión de períodos, usuarios y respaldos.

Cada sección de módulo proporciona:
*   Una breve descripción de su propósito general.
*   Una lista de las funcionalidades clave, a menudo haciendo referencia a las páginas PHP principales involucradas (ej. `planilla_inscripcion.php`, `administrar_planilla_estudiantes.php`).

---

## 3. Uso en el Sistema SWGA

Este documento es el contenido que se muestra cuando el usuario selecciona la opción "Manual de Usuario" en el nuevo "Módulo de Ayuda" (`pages/menu_ayuda.php`). Es renderizado dinámicamente a HTML por el visor de documentos (`pages/view_document.php`), lo que permite que el manual sea actualizado fácilmente editando este archivo Markdown.
