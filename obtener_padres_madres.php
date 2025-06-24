<?php
require_once "conn/conexion.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT p.id AS padre_id, p.nombre AS padre_nombre, p.apellido AS padre_apellido, p.celular AS padre_celular, p.email AS padre_email,
                               m.id AS madre_id, m.nombre AS madre_nombre, m.apellido AS madre_apellido, m.celular AS madre_celular, m.email AS madre_email
                        FROM estudiantes e
                        JOIN padres p ON e.padre_id = p.id
                        JOIN madres m ON e.madre_id = m.id
                        WHERE e.id = :id");

$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
?>