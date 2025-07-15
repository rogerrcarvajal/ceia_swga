<?php
require_once __DIR__ . '/../src/config.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Solicitud inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id'])) {
        $response['message'] = 'Error: ID de estudiante no proporcionado.';
        echo json_encode($response);
        exit;
    }

    try {
        $sql = "UPDATE estudiantes SET 
                    nombre_completo = :nombre_completo,
                    apellido_completo = :apellido_completo,
                    fecha_nacimiento = :fecha_nacimiento, 
                    lugar_nacimiento = :lugar_nacimiento,
                    nacionalidad = :nacionalidad, 
                    idioma = :idioma, 
                    direccion = :direccion, 
                    telefono_casa = :telefono_casa, 
                    telefono_movil = :telefono_movil, 
                    telefono_emergencia = :telefono_emergencia, 
                    fecha_inscripcion = :fecha_inscripcion, 
                    recomendado_por = :recomendado_por,
                    edad_estudiante = :edad_estudiante,
                    estudiante_hermanos = :estudiante_hermanos,
                    colegios_anteriores = :colegios_anteriores,
                    staff = :staff, 
                WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            ':id' => $_POST['id'],
            ':nombre_completo' => $_POST['nombre_completo'] ?? '',
            ':apellido_completo' => $_POST['apellido_completo'] ?? '',
            ':fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
            ':lugar_nacimiento' => $_POST['lugar_nacimiento'] ?? '',
            ':nacionalidad' => $_POST['nacionalidad'] ?? '',
            ':idioma' => $_POST['idioma'] ?? '',
            ':direccion' => $_POST['direccion'] ?? '',
            ':telefono_casa' => $_POST['telefono_casa'] ?? '',
            ':telefono_movil' => $_POST['telefono_movil'] ?? '',
            ':telefono_emergencia' => $_POST['telefono_emergencia'] ?? '',
            ':fecha_inscripcion' => $_POST['fecha_inscripcion'] ?: null,
            ':recomendado_por' => $_POST['recomendado_por'] ?? '',
            ':edad_estudiante' => (int)($_POST['edad_estudiante'] ?? 0),
            ':estudiante_hermanos' => $_POST['estudiante_hermanos'] ?? '',
            ':colegios_anteriores' => $_POST['colegios_anteriores'] ?? '',
            ':staff' => isset($_POST['staff']) ? 1 : 0,
        ]);

        $response['status'] = 'exito';
        $response['message'] = '✅ Datos del estudiante actualizados correctamente.';

    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>