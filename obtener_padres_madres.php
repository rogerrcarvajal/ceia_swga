<?php
require_once "conn/conexion.php";

$id = $_GET['id'] ?? 0;

// Usamos LEFT JOIN para que no falle si un estudiante no tiene padre o madre asignado.
// Seleccionamos todos los campos del formulario.
$stmt = $conn->prepare("SELECT 
                            p.id AS padre_id, p.nombre AS padre_nombre, p.apellido AS padre_apellido, p.fecha_nacimiento AS padre_fecha_nacimiento, p.cedula_pasaporte AS padre_cedula_pasaporte, p.nacionalidad AS padre_nacionalidad, p.idioma AS padre_idioma, p.profesion AS padre_profesion, p.empresa AS padre_empresa, p.telefono_trabajo AS padre_telefono_trabajo, p.celular AS padre_celular, p.email AS padre_email,
                            m.id AS madre_id, m.nombre AS madre_nombre, m.apellido AS madre_apellido, m.fecha_nacimiento AS madre_fecha_nacimiento, m.cedula_pasaporte AS madre_cedula_pasaporte, m.nacionalidad AS madre_nacionalidad, m.idioma AS madre_idioma, m.profesion AS madre_profesion, m.empresa AS madre_empresa, m.telefono_trabajo AS madre_telefono_trabajo, m.celular AS madre_celular, m.email AS madre_email
                        FROM estudiantes e
                        LEFT JOIN padres p ON e.padre_id = p.id
                        LEFT JOIN madres m ON e.madre_id = m.id
                        WHERE e.id = :id");

$stmt->execute([':id' => $id]);
// Usamos fetchAll y tomamos el primer resultado para asegurar que obtenemos un array aunque no haya resultados.
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data = $results[0] ?? []; // Devuelve un array vacío si no hay resultados

header('Content-Type: application/json');
echo json_encode($data);
?>
