<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECCIÓN: Usamos consistentemente 'estudiante_id' como el ID que viene del formulario.
    $estudiante_id = $_POST['estudiante_id'] ?? null;
    if (!$estudiante_id) {
        $response['message'] = 'Error: ID de Estudiante no proporcionado para actualizar la ficha.';
    } else {
        try {
            // CORRECCIÓN: Aseguramos que la tabla sea 'salud_estudiantil' y que los campos sean correctos.
            // El SQL ya estaba bien, usando 'id_where' como placeholder
            $sql = "UPDATE salud_estudiantil SET
                    completado_por = :completado_por,
                    fecha_salud = :fecha_salud,
                    contacto_emergencia = :contacto_emergencia, 
                    relacion_emergencia = :relacion_emergencia,
                    telefono1 = :telefono1, 
                    telefono2 = :telefono2, 
                    observaciones = :observaciones, 
                    dislexia = :dislexia, 
                    atencion = :atencion, 
                    otros = :otros, 
                    info_adicional = :info_adicional,
                    problemas_oido_vista = :problemas_oido_vista,
                    fecha_examen = :fecha_examen,
                    autorizo_medicamentos = :autorizo_medicamentos,
                    medicamentos_actuales = :medicamentos_actuales,
                    autorizo_emergencia = :autorizo_emergencia
                WHERE estudiante_id = :id_where"; // UWHERE estudiante_id = :id_where;samos la Primary Key

        $stmt = $conn->prepare($sql);

            // CORRECCIÓN: Vinculamos la variable correcta '$estudiante_id' al placeholder.
            $stmt->execute([
            ':id_where' => $estudiante_id,
            ':completado_por' => $_POST['completado_por'] ?? '', 
            ':fecha_salud' => $_POST['fecha_salud'] ?: null,
            ':contacto_emergencia' => $_POST['contacto_emergencia'] ?? '', 
            ':relacion_emergencia' => $_POST['relacion_emergencia'] ?? '', 
            ':telefono1' => $_POST['telefono1']?? '', 
            ':telefono2' => $_POST['telefono2']?? '', 
            ':observaciones' => $_POST['observaciones'] ?? '', 
            ':dislexia' => isset($_POST['dislexia']) ? 1 : 0, 
            ':atencion' => isset($_POST['atencion']) ? 1 : 0, 
            ':otros' => isset($_POST['otros']) ? 1 : 0, 
            ':info_adicional' => $_POST['info_adicional'] ?? '', 
            ':problemas_oido_vista' => $_POST['problemas_oido_vista'] ?? '', 
            ':fecha_examen' => $_POST['fecha_examen'] ?: null,
            ':autorizo_medicamentos' => isset($_POST['autorizo_medicamentos']) ? 1 : 0, 
            ':medicamentos_actuales' => $_POST['medicamentos_actuales'] ?? '',
            ':autorizo_emergencia' => isset($_POST['autorizo_emergencia']) ? 1 : 0
        
        ]);

        $response['status'] = 'exito';
        $response['message'] = '✅ Ficha médica actualizada correctamente.';
        
    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}
}
echo json_encode($response);
?>