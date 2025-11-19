<?php
/**
 * Este archivo actúa como un punto de entrada seguro y accesible desde la URL.
 * Su única función es incluir el generador de PDF real desde el directorio 'src',
 * el cual no es accesible directamente por motivos de seguridad del servidor web.
 * 
 * Al recibir el 'id' por la URL (GET), se lo pasa implícitamente al script incluido.
 */
require_once __DIR__ . '/../src/reports_generators/generar_permiso_staff_pdf.php';
?>