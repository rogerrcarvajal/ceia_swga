<?php
require_once __DIR__ . '/../src/lib/Parsedown.php';
$Parsedown = new Parsedown();
$page_title = "Documentación Técnica: Módulo Ayuda";

$markdown_content = <<<'MARKDOWN'
# Análisis de Funcionalidad: Módulo de Ayuda

Este documento describe la estructura y el propósito del Módulo de Ayuda.

---

### Arquitectura y Propósito

El Módulo de Ayuda (`menu_ayuda.php`) funciona como un portal de documentación centralizado para todo el sistema SWGA. Su objetivo es proporcionar dos niveles de soporte:

1.  **Soporte al Usuario Final:** A través del **Manual de Usuario** (`doc_manual_usuario.php`), se ofrece una guía de alto nivel sobre la navegabilidad y el flujo de trabajo general del sistema, permitiendo a los administradores y operadores comprender cómo utilizar cada módulo de manera efectiva.

2.  **Soporte Técnico:** A través de una serie de documentos técnicos (`doc_modulo_....php`), se proporciona un análisis detallado de la arquitectura, los componentes y la lógica de negocio de cada módulo individual. Esta documentación es invaluable para los desarrolladores, el personal de TI o cualquier persona que necesite comprender el funcionamiento interno del sistema para realizar mantenimiento, solucionar problemas o desarrollar nuevas funcionalidades.

### Componentes Clave

-   **`menu_ayuda.php`**: La página principal del módulo, que contiene los enlaces a toda la documentación.
-   **`doc_manual_usuario.php`**: El manual de usuario general.
-   **`doc_modulo_....php`**: Una página dedicada para la documentación técnica de cada módulo principal del sistema (Estudiantes, Staff, Late-Pass, Reportes, Mantenimiento y este mismo módulo de Ayuda).
-   **`view_document.php`**: Un script de plantilla que se reutiliza para renderizar el contenido Markdown de manera consistente en todas las páginas de documentación, promoviendo la reutilización de código (principio DRY - Don't Repeat Yourself).
-   **`Parsedown.php`**: Una librería de PHP de terceros que se utiliza para convertir el texto escrito en formato Markdown a HTML, permitiendo que la documentación sea fácil de escribir y mantener.

### Conclusión

El Módulo de Ayuda es un componente fundamental para la sostenibilidad y mantenibilidad a largo plazo del sistema. Al centralizar tanto la documentación de usuario como la técnica, se asegura que todo el conocimiento sobre el sistema esté accesible y organizado, reduciendo la dependencia de desarrolladores individuales y facilitando la capacitación de nuevo personal.
MARKDOWN;

require_once __DIR__ . '/view_document.php';
