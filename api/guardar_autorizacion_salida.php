<?php
// api/guardar_autorizacion_salida.php

require_once __DIR__ . '/../src/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validaciones de seguridad y de datos...
    $estudiante_id = $_POST['estudiante_id'];
    $fecha_salida = $_POST['fecha_salida'];
    $hora_salida = $_POST['hora_salida'];
    $retirado_por_nombre = trim($_POST['retirado_por_nombre']);
    $retirado_por_parentesco = trim($_POST['retirado_por_parentesco']);
    $motivo = trim($_POST['motivo']);
    $usuario_id = $_SESSION['usuario']['id'];

    try {
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

        // Redirigir al script que genera el PDF
        header('Location: /ceia_swga/src/reports_generators/generar_pdf_salida.php?id=' . $nuevaAutorizacionId);
        exit;

    } catch (PDOException $e) {
        // Manejar error
        die("Error al guardar la autorización: " . $e->getMessage());
    }
}