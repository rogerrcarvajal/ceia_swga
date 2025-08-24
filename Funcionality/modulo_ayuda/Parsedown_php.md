# Documentación del Archivo: `src/lib/Parsedown.php`

## 1. Propósito del Archivo

Este archivo contiene la clase `Parsedown`, una **librería de terceros** escrita en PHP. Su propósito fundamental es tomar texto formateado en **Markdown** y convertirlo a **HTML**. Es una herramienta ligera, rápida y fácil de integrar, lo que la convierte en una solución ideal para renderizar contenido dinámico o estático escrito en Markdown dentro de aplicaciones web.

---

## 2. Funcionalidad Clave

La clase `Parsedown` proporciona una interfaz sencilla para realizar la conversión de Markdown a HTML:

*   **Método `text($markdown_content)`**: Este es el método principal y más utilizado. Recibe una cadena de texto formateada en Markdown como entrada y devuelve la cadena de texto equivalente en HTML.

    **Ejemplo de uso:**
    ```php
    require_once __DIR__ . '/Parsedown.php';

    $Parsedown = new Parsedown();

    $markdown_text = "# Título\n\nEsto es un **texto** en *Markdown*.";
    $html_output = $Parsedown->text($markdown_text);

    echo $html_output;
    // Salida: <h1>Título</h1><p>Esto es un <strong>texto</strong> en <em>Markdown</em>.</p>
    ```

---

## 3. Uso en el Sistema SWGA

En el sistema SWGA, `Parsedown.php` es un componente esencial del **Módulo de Ayuda**. Es utilizado específicamente por el archivo `pages/view_document.php`.

*   **`pages/view_document.php`**: Este script lee los archivos de documentación (`.md`) almacenados en el directorio `Funcionality/` y utiliza la clase `Parsedown` para convertir su contenido a HTML antes de mostrarlo al usuario en el navegador. Esto permite que la documentación sea escrita en un formato sencillo (Markdown) y sea presentada de forma profesional en la interfaz del sistema.

---

## 4. Licencia

Parsedown es una librería de código abierto distribuida bajo la licencia MIT. La información completa de la licencia se encuentra dentro del propio archivo `Parsedown.php`.