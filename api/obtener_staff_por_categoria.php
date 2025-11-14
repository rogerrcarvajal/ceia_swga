<?php
// api/obtener_staff_por_categoria.php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/config.php';

$response = ['status' => 'error', 'mensaje' => 'Categoría no proporcionada.'];

if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $categoria = $_GET['categoria'];

    try {
        // Obtener el ID del período escolar activo
        $periodo_activo_id = $conn->query("SELECT id FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetchColumn();

        if (!$periodo_activo_id) {
            throw new Exception("No hay un período escolar activo definido.");
        }

        // Consulta para obtener el personal de la categoría y su posición en el período activo
        $sql = "SELECT 
                    p.id, 
                    p.nombre_completo, 
                    p.cedula,
                    pp.posicion
                FROM profesores p
                LEFT JOIN profesor_periodo pp ON p.id = pp.profesor_id AND pp.periodo_id = :periodo_id
                WHERE p.categoria = :categoria
                ORDER BY p.nombre_completo ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':categoria' => $categoria,
            ':periodo_id' => $periodo_activo_id
        ]);

        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Se devuelve éxito incluso si no hay staff, el frontend manejará el array vacío.
        $response = ['status' => 'exito', 'staff' => $staff];

    } catch (Exception $e) {
        $response['mensaje'] = 'Error de base de datos: ' . $e->getMessage();
    }
} 

echo json_encode($response);
?>
