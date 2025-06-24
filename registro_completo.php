<?php
session_start();
require_once "conn/conexion.php";

$mensaje = "";

// Obtener período activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Ve al menú Mantenimiento para crear uno.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();

        // Estudiante
        $nombre = $_POST['nombre_completo'];
        $direccion = $_POST['direccion'];
        $tel_casa = $_POST['telefono_casa'] ?: null;
        $tel_movil = $_POST['telefono_movil'] ?: null;
        $tel_emer = $_POST['telefono_emergencia'] ?: null;
        $grado = $_POST['grado_ingreso'];
        $activo = isset($_POST['activo']) && $_POST['activo'] === 'on';

        $stmt = $conn->prepare("INSERT INTO estudiantes (
            nombre_completo, direccion, telefono_casa, telefono_movil, telefono_emergencia, grado_ingreso, activo, periodo_id
        ) VALUES (
            :nombre, :direccion, :tel_casa, :tel_movil, :tel_emer, :grado, :activo, :periodo_id
        )");

        $stmt->execute([
            ':nombre' => $nombre,
            ':direccion' => $direccion,
            ':tel_casa' => $tel_casa,
            ':tel_movil' => $tel_movil,
            ':tel_emer' => $tel_emer,
            ':grado' => $grado,
            ':activo' => $activo,
            ':periodo_id' => $periodo['id']
        ]);

        $estudiante_id = $conn->lastInsertId();

        // Padre
        $stmt = $conn->prepare("INSERT INTO padres (nombre, apellido, celular, email) VALUES (:n, :a, :c, :e)");
        $stmt->execute([
            ':n' => $_POST['padre_nombre'],
            ':a' => $_POST['padre_apellido'],
            ':c' => $_POST['padre_celular'],
            ':e' => $_POST['padre_email']
        ]);
        $padre_id = $conn->lastInsertId();

        // Madre
        $stmt = $conn->prepare("INSERT INTO madres (nombre, apellido, celular, email) VALUES (:n, :a, :c, :e)");
        $stmt->execute([
            ':n' => $_POST['madre_nombre'],
            ':a' => $_POST['madre_apellido'],
            ':c' => $_POST['madre_celular'],
            ':e' => $_POST['madre_email']
        ]);
        $madre_id = $conn->lastInsertId();

        // Actualizar estudiante con padres
        $stmt = $conn->prepare("UPDATE estudiantes SET padre_id = :padre, madre_id = :madre WHERE id = :id");
        $stmt->execute([':padre' => $padre_id, ':madre' => $madre_id, ':id' => $estudiante_id]);

        // Ficha médica
        $stmt = $conn->prepare("INSERT INTO salud_estudiantil (
            estudiante_id, contacto_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional
        ) VALUES (
            :id, :contacto, :tel1, :tel2, :obs, :dislexia, :atencion, :otros, :info
        )");
        $stmt->execute([
            ':id' => $estudiante_id,
            ':contacto' => $_POST['contacto_emergencia'],
            ':tel1' => $_POST['telefono_emergencia1'],
            ':tel2' => $_POST['telefono_emergencia2'],
            ':obs' => $_POST['observaciones'],
            ':dislexia' => isset($_POST['dislexia']) ? 1 : 0,
            ':atencion' => isset($_POST['atencion']) ? 1 : 0,
            ':otros' => isset($_POST['otros']) ? 1 : 0,
            ':info' => $_POST['info_adicional']
        ]);

        $conn->commit();
        $mensaje = "✅ Registro completo realizado con éxito para el estudiante en el período: <strong>" . htmlspecialchars($periodo['nombre_periodo']) . "</strong>";

    } catch (PDOException $e) {
        $conn->rollBack();
        $mensaje = "❌ Error en el proceso: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Completo de Inscripción</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-image: url('img/fondo.jpg'); background-size: cover;">
    <?php include 'navbar.php'; ?>
    <div class="login-box">
        <h2>Registro Completo de Inscripción</h2>
        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST">
            <!-- DATOS DEL ESTUDIANTE -->
            <h3>Datos del Estudiante</h3>
            <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
            <textarea name="direccion" placeholder="Dirección" required></textarea>
            <input type="text" name="telefono_casa" placeholder="Teléfono de casa">
            <input type="text" name="telefono_movil" placeholder="Teléfono móvil">
            <input type="text" name="telefono_emergencia" placeholder="Teléfono emergencia">
            <input type="text" name="grado_ingreso" placeholder="Grado de ingreso" required>
            <label><input type="checkbox" name="activo"> Estudiante activo</label><br><br>

            <!-- PADRE -->
            <h3>Datos del Padre</h3>
            <input type="text" name="padre_nombre" placeholder="Nombre" required>
            <input type="text" name="padre_apellido" placeholder="Apellido" required>
            <input type="text" name="padre_celular" placeholder="Celular">
            <input type="email" name="padre_email" placeholder="Correo electrónico">

            <!-- MADRE -->
            <h3>Datos de la Madre</h3>
            <input type="text" name="madre_nombre" placeholder="Nombre" required>
            <input type="text" name="madre_apellido" placeholder="Apellido" required>
            <input type="text" name="madre_celular" placeholder="Celular">
            <input type="email" name="madre_email" placeholder="Correo electrónico">

            <!-- FICHA MÉDICA -->
            <h3>Ficha Médica</h3>
            <input type="text" name="contacto_emergencia" placeholder="Nombre del contacto de emergencia" required>
            <input type="text" name="telefono_emergencia1" placeholder="Teléfono 1" required>
            <input type="text" name="telefono_emergencia2" placeholder="Teléfono 2">
            <textarea name="observaciones" placeholder="Observaciones médicas"></textarea>
            <label><input type="checkbox" name="dislexia"> Dislexia</label><br>
            <label><input type="checkbox" name="atencion"> Déficit de Atención</label><br>
            <label><input type="checkbox" name="otros"> Otros</label><br>
            <textarea name="info_adicional" placeholder="Información adicional de salud"></textarea><br>

            <button type="submit">Guardar Inscripción Completa</button>
        </form>
    </div>
</body>
</html>