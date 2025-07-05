<?php
require_once __DIR__ . '/../src/config.php';

// Validar que el ID exista
if (empty($_POST['id'])) {
    echo "Error: ID de estudiante no proporcionado.";
    exit;
}

// Asignar todas las variables desde _POST
$id = $_POST['id'];
$nombre_completo = $_POST['nombre_completo'] ?? '';
$apellido_completo = $_POST['apellido_completo'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
$lugar_nacimiento = $_POST['lugar_nacimiento'] ?? '';
$nacionalidad = $_POST['nacionalidad'] ?? '';
$idioma = $_POST['idioma'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono_casa = $_POST['telefono_casa'] ?? '';
$telefono_movil = $_POST['telefono_movil'] ?? '';
$telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
$grado_ingreso = $_POST['grado_ingreso'] ?? '';
$fecha_inscripcion = $_POST['fecha_inscripcion'] ?? null;
$recomendado_por = $_POST['recomendado_por'] ?? '';
$edad_estudiante = $_POST['edad_estudiante'] ?? 0;
$staff = isset($_POST['staff']) ? 1 : 0;
$activo = isset($_POST['activo']) ? 1 : 0;

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
        WHERE id = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':nombre_completo' => $nombre_completo,
    ':apellido_completo' => $apellido_completo,
    ':fecha_nacimiento' => $fecha_nacimiento,
    ':lugar_nacimiento' => $lugar_nacimiento,
    ':nacionalidad' => $nacionalidad,
    ':idioma' => $idioma,
    ':direccion' => $direccion,
    ':telefono_casa' => $telefono_casa,
    ':telefono_movil' => $telefono_movil,
    ':telefono_emergencia' => $telefono_emergencia,
    ':grado_ingreso' => $grado_ingreso,
    ':fecha_inscripcion' => $fecha_inscripcion,
    ':recomendado_por' => $recomendado_por,
    ':edad_estudiante' => $edad_estudiante,
    ':staff' => $staff,
    ':activo' => $activo,
    ':id' => $id
]);

echo "✅ Datos del estudiante actualizados correctamente.";
?>
