<?php
// Paso 1: Iniciar la sesión. ESTO DEBE SER LO PRIMERO.
session_start();

// Paso 2: Establecer el tipo de contenido a JSON. Esto asegura que la respuesta sea siempre JSON.
header('Content-Type: application/json');

// Paso 3: Verificar que el usuario ha iniciado sesión.
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['rol'])) {
    // Si no hay sesión, se devuelve un error JSON y se detiene el script.
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. Sesión no válida.']);
    exit();
}

// Paso 4: Verificar que el usuario tiene permisos de administrador.
if ($_SESSION['usuario']['rol'] !== 'admin') {
    // Si no es admin, se devuelve un error JSON y se detiene.
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No tiene permisos de administrador.']);
    exit();
}

// Paso 5: Solo si todas las verificaciones de seguridad pasan, incluimos la configuración.
require_once __DIR__ . '/../src/config.php';

// Paso 6: Verificamos que el método de la solicitud sea POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no válido.']);
    exit();
}

// --- Inicio de la Lógica de Negocio ---
$response = ['status' => 'error', 'message' => 'Ocurrió un error inesperado.'];

$profesor_id = $_POST['profesor_id'] ?? null;
$periodo_id = $_POST['periodo_id'] ?? null;
$posicion = $_POST['posicion'] ?? null;
$homeroom = $_POST['homeroom_teacher'] ?? null;

if ($profesor_id && $periodo_id && $posicion) {
    try {
        $sql = "INSERT INTO profesor_periodo (profesor_id, periodo_id, posicion, homeroom_teacher)
                VALUES (:profesor_id, :periodo_id, :posicion, :homeroom_teacher)";
        $stmt = $conn->prepare($sql);
        
        $homeroom_value = (empty($homeroom) || $homeroom === 'N/A') ? null : $homeroom;

        $stmt->execute([
            ':profesor_id' => $profesor_id,
            ':periodo_id' => $periodo_id,
            ':posicion' => $posicion,
            ':homeroom_teacher' => $homeroom_value
        ]);
        
        $response = ['status' => 'success', 'message' => 'Profesor asignado correctamente al período.'];

    } catch (PDOException $e) {
        if ($e->getCode() == '23505') {
             $response['message'] = 'Error: Este profesor ya está asignado a este período escolar.';
        } else {
             // Para depuración, podrías registrar el error real en un log.
             // error_log('Error en asignar_profesor: ' . $e->getMessage());
             $response['message'] = 'Error de base de datos al intentar guardar.';
        }
    }
} else {
    $response['message'] = 'Datos incompletos. Asegúrese de seleccionar un profesor, una posición y un período.';
}

// Devolver la respuesta final en formato JSON
echo json_encode($response);
?>