<?php
require_once __DIR__ . '/../src/config.php';
date_default_timezone_set('America/Caracas');

$data = json_decode(file_get_contents('php://input'), true);
$estudiante_id = $data['estudiante_id'];

$hora_actual = date('H:i:s');
$fecha_actual = date('Y-m-d');
$semana_del_anio = date('W');
$dia_de_la_semana = date('N');

// Lógica para determinar si es tarde (después de las 8:05 AM)
$es_tarde = (strtotime($hora_actual) > strtotime('08:05:59'));

// 1. Insertar el registro individual
// ...

// 2. Si es tarde, actualizar el resumen semanal
if ($es_tarde) {
    // Obtener conteo actual
    // Incrementar conteo
    // Definir color y mensaje de alerta
}

// 3. Devolver respuesta JSON con la información de la alerta
echo json_encode(['status' => 'exito', 'conteo' => $conteo, 'mensaje' => $mensaje]);
?>