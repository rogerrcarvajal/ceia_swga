<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
date_default_timezone_set('America/Caracas');

$input = json_decode(file_get_contents('php://input'), true);
$estudiante_id = filter_var($input['estudiante_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$estudiante_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de estudiante no válido.']);
    exit();
}

try {
    $conn->beginTransaction(); // Iniciar transacción para asegurar consistencia

    // --- 1. Obtener datos clave ---
    $periodo_activo = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$periodo_activo) throw new Exception("No hay período escolar activo.");
    $periodo_id = $periodo_activo['id'];
    
    // ... (Obtener datos del estudiante)

    // --- 2. ¡VALIDACIÓN CLAVE! Evitar registros duplicados por día ---
    $fecha_registro = date('Y-m-d');
    $stmt_check = $conn->prepare("SELECT id FROM llegadas_tarde WHERE estudiante_id = ? AND fecha_registro = ?");
    $stmt_check->execute([$estudiante_id, $fecha_registro]);
    if ($stmt_check->rowCount() > 0) {
        throw new Exception("Este estudiante ya fue registrado hoy.");
    }
    
    // --- 3. Definir variables de tiempo y registrar la llegada ---
    $hora_llegada = date('H:i:s');
    $semana_del_anio = date('W');
    $dia_de_la_semana = date('N');
    $es_tarde = (strtotime($hora_llegada) > strtotime('08:05:59'));

    $sql_insert = "INSERT INTO llegadas_tarde (estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana) VALUES (?, ?, ?, ?, ?)";
    $conn->prepare($sql_insert)->execute([$estudiante_id, $hora_llegada, $fecha_registro, $semana_del_anio, $dia_de_la_semana]);

    // --- 4. Lógica de Alertas y Strikes (MEJORADA) ---
    $conteo_tardes = 0;
    $mensaje = "Llegada a tiempo registrada.";

    if ($es_tarde) {
        // ... (Obtener el resumen semanal actual)
        $stmt_resumen = $conn->prepare("SELECT id, conteo_tardes FROM latepass_resumen_semanal WHERE estudiante_id = ? AND periodo_id = ? AND semana_del_anio = ?");
        $stmt_resumen->execute([$estudiante_id, $periodo_id, $semana_del_anio]);
        $resumen = $stmt_resumen->fetch();
        
        if ($resumen) {
            $conteo_tardes = $resumen['conteo_tardes'] + 1;
        } else {
            $conteo_tardes = 1;
        }

        // Definir el mensaje según el número de strikes
        if ($conteo_tardes == 1) $mensaje = "Llegada Tarde - Primer Strike";
        elseif ($conteo_tardes == 2) $mensaje = "Llegada Tarde - Segundo Strike";
        else $mensaje = "Llegada Tarde - TERCER STRIKE. Notificar al representante.";

        // Si ya superó los 3 strikes, el mensaje es más severo
        if ($resumen && $resumen['conteo_tardes'] >= 3) {
            $mensaje = "Máximo de strikes alcanzado esta semana. Notificar al representante.";
        }
        
        // Actualizar o insertar el resumen semanal
        if($resumen) {
            $sql_update = "UPDATE latepass_resumen_semanal SET conteo_tardes = ?, ultimo_mensaje = ? WHERE id = ?";
            $conn->prepare($sql_update)->execute([$conteo_tardes, $mensaje, $resumen['id']]);
        } else {
            // ... (Insertar nuevo resumen con el mensaje)
        }
    }

    $conn->commit(); // Confirmar todos los cambios en la base de datos

    // --- 5. Preparar la respuesta JSON ---
    $response = [
        'status' => 'exito',
        'nombre_completo' => $estudiante['nombre_completo'] . ' ' . $estudiante['apellido_completo'],
        'grado' => $estudiante['grado_cursado'],
        'hora_llegada' => $hora_llegada,
        'es_tarde' => $es_tarde,
        'conteo_tardes' => $conteo_tardes,
        'mensaje' => $mensaje
    ];

} catch (Exception $e) {
    $conn->rollBack(); // Revertir cambios si algo falla
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>