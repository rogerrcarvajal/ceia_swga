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
                    grado_ingreso = :grado_ingreso,
                    fecha_inscripcion = :fecha_inscripcion, 
                    recomendado_por = :recomendado_por,
                    edad_estudiante = :edad_estudiante, 
                    staff = :staff, 
                    activo = :activo
                WHERE estudiante_id = :id";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            ':id' => $_POST['estudiante_id'],
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
            ':grado_ingreso' => $_POST['grado_ingreso'] ?? '',
            ':fecha_inscripcion' => $_POST['fecha_inscripcion'] ?: null,
            ':recomendado_por' => $_POST['recomendado_por'] ?? '',
            ':edad_estudiante' => (int)($_POST['edad_estudiante'] ?? 0),
            ':staff' => isset($_POST['staff']) ? 1 : 0,
            ':activo' => isset($_POST['activo']) ? 1 : 0
        ]);

        $response['status'] = 'exito';
        $response['message'] = '✅ Datos del estudiante actualizados correctamente.';

    } catch (PDOException $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>