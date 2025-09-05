# Documentación del Archivo: `pages/control_acceso.php`

## 1. Propósito del Archivo

Este archivo es la **interfaz de operación en tiempo real** para el control de acceso al plantel. Está diseñada para ser utilizada en los puntos de entrada y salida (ej. garita de seguridad) con un escáner de códigos QR o para la introducción manual de cédulas de identidad.

Su objetivo es proporcionar una forma rápida y eficiente de registrar los movimientos de estudiantes, personal y vehículos.

---

## 2. Estructura de la Interfaz (HTML)

La página tiene un diseño deliberadamente simple para facilitar su uso en un entorno de trabajo rápido.

*   **Campo de Entrada Único**: El elemento central es un campo de texto (`<input id="qr-input">`). Está configurado con `autofocus` para que el cursor siempre esté en este campo, listo para recibir datos de un escáner de QR (que funciona como un teclado) o para que un operador escriba una cédula.

*   **Áreas de Feedback Dinámico**: La página no muestra datos por sí misma, sino que define varios contenedores `<div>` vacíos que son manipulados por JavaScript para dar feedback al operador:
    *   `#qr-result`: Un `div` principal para mostrar mensajes de estado (ej. "Bienvenido, Juan Pérez", "Error: Cédula no encontrada").
    *   `#qr-code`: Un `div` destinado a mostrar la imagen del código QR de la persona o vehículo escaneado, para una verificación visual.
    *   `#log-registros`: Un `div` que probablemente se usa para mostrar un historial de los últimos movimientos registrados durante la sesión.

*   **Formulario**: El campo de entrada está dentro de un formulario (`<form id="qr-form">`), pero su envío no recarga la página. La sumisión es interceptada y manejada completamente por JavaScript.

---

## 3. Vínculo con el Frontend

La funcionalidad completa de esta página depende de manera crítica del siguiente archivo, que se carga al final:

```html
<script src="/ceia_swga/public/js/control_acceso.js"></script>
```

Este script es el cerebro de la página y es responsable de:
*   Capturar la entrada de datos del formulario.
*   Determinar si el dato es de un estudiante, personal o vehículo.
*   Comunicarse con las APIs del backend para consultar el estado actual y registrar el nuevo movimiento.
*   Actualizar en tiempo real todos los elementos de feedback (`#qr-result`, `#qr-code`, etc.) con la respuesta del servidor.
