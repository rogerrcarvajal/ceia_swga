<?php
require_once __DIR__ . '/../src/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'] ?? null;
    $fecha_salida = $_POST['fecha_salida'] ?? null;
    $hora_salida = $_POST['hora_salida'] ?? null;
    $motivo = trim($_POST['motivo'] ?? '');
    $usuario_id = $_SESSION['usuario']['id'] ?? null;
    $autorizado_chk = $_POST['autorizado_chk'] ?? null;

    $retirado_por_nombre = '';
    $retirado_por_parentesco = '';

    if (!$estudiante_id || !$fecha_salida || !$hora_salida || !$usuario_id || !$autorizado_chk) {
        die("Error: Faltan datos requeridos.");
    }

    try {
        if ($autorizado_chk === 'padre') {
            $padre_id = $_POST['retirado_por_padre_id'] ?? null;
            $stmt = $conn->prepare("SELECT nombre_completo FROM padres WHERE id = :id");
            $stmt->execute([':id' => $padre_id]);
            $retirado_por_nombre = $stmt->fetchColumn();
            $retirado_por_parentesco = 'Padre';
        } elseif ($autorizado_chk === 'madre') {
            $madre_id = $_POST['retirado_por_madre_id'] ?? null;
            $stmt = $conn->prepare("SELECT nombre_completo FROM madres WHERE id = :id");
            $stmt->execute([':id' => $madre_id]);
            $retirado_por_nombre = $stmt->fetchColumn();
            $retirado_por_parentesco = 'Madre';
        } elseif ($autorizado_chk === 'otro') {
            $retirado_por_nombre = trim($_POST['retirado_por_nombre'] ?? '');
            $retirado_por_parentesco = trim($_POST['retirado_por_parentesco'] ?? '');
        }

        if (empty($retirado_por_nombre)) {
            die("Error: No se pudo determinar la persona que retira.");
        }

        $stmt = $conn->prepare(
            "INSERT INTO autorizaciones_salida (estudiante_id, fecha_salida, hora_salida, retirado_por_nombre, retirado_por_parentesco, motivo, generado_por_usuario_id)
             VALUES (:est_id, :fecha, :hora, :nombre, :parentesco, :motivo, :user_id)"
        );
        $stmt->execute([
            ':est_id' => $estudiante_id,
            ':fecha' => $fecha_salida,
            ':hora' => $hora_salida,
            ':nombre' => $retirado_por_nombre,
            ':parentesco' => $retirado_por_parentesco,
            ':motivo' => $motivo,
            ':user_id' => $usuario_id
        ]);

        $nuevaAutorizacionId = $conn->lastInsertId();

<<<<<<< HEAD
        header('Location: /ceia_swga/src/reports_generators/generar_pdf_salida.php?id=' . $nuevaAutorizacionId);
        exit;
=======
        if ($nuevaAutorizacionId) {
            echo json_encode([
                'status' => 'exito',
                'mensaje' => 'Autorización de salida para el estudiante guardada exitosamente.',
                'id' => $nuevaAutorizacionId
            ]);
        } else {
            throw new Exception('No se pudo guardar el registro en la base de datos.');
        }
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0

    } catch (PDOException $e) {
        die("Error al guardar la autorización: " . $e->getMessage());
    }
}