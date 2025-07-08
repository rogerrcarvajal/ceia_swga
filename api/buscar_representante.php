<?php
session_start();
header('Content-Type: application/json');

// Medida de seguridad básica
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['encontrado' => false, 'error' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/../src/config.php';

$tipo = $_GET['tipo'] ?? ''; // 'padre' o 'madre'
$cedula = $_GET['cedula'] ?? '';

if (empty($tipo) || empty($cedula)) {
    echo json_encode(['encontrado' => false, 'error' => 'Faltan parámetros']);
    exit();
}

// Validar que el tipo sea uno de los esperados para evitar inyección SQL
if ($tipo !== 'padre' && $tipo !== 'madre') {
    echo json_encode(['encontrado' => false, 'error' => 'Tipo de representante no válido']);
    exit();
}

// Construir la consulta de forma segura
$tabla = ($tipo === 'padre') ? 'padres' : 'madres';
$columna_cedula = "{$tipo}_cedula_pasaporte";
$columna_nombre = "{$tipo}_nombre";
$columna_apellido = "{$tipo}_apellido";

$sql = "SELECT id, {$columna_nombre}, {$columna_apellido} FROM {$tabla} WHERE {$columna_cedula} = :cedula LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':cedula' => $cedula]);
$representante = $stmt->fetch(PDO::FETCH_ASSOC);

if ($representante) {
    echo json_encode([
        'encontrado' => true,
        'id' => $representante['id'],
        'nombre' => htmlspecialchars($representante[$columna_nombre] . ' ' . $representante[$columna_apellido])
    ]);
} else {
    echo json_encode(['encontrado' => false]);
}
?>