<?php
session_start();
require_once "conn/conexion.php";

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $nombre_completo = $_POST['nombre_completo'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $lugar_nacimiento = $_POST['lugar_nacimiento'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? '';
        $idioma = $_POST['idioma'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono_casa = $_POST['telefono_casa'] ?: null;
        $telefono_movil = $_POST['telefono_movil'] ?: null;
        $telefono_emergencia = $_POST['telefono_emergencia'] ?: null;
        $grado_ingreso = $_POST['grado_ingreso'] ?? '';
        $fecha_inscripcion = $_POST['fecha_inscripcion'] ?? '';
        $recomendado_por = $_POST['recomendado_por'] ?? '';
        $padre_id = $_POST['padre_id'] ?? '';
        $madre_id = $_POST['madre_id'] ?? '';
        $activo = isset($_POST['activo']) && $_POST['activo'] === 'on';
        $periodo_id = $_POST['periodo_id'] ?? '';

        $sql_est = "INSERT INTO estudiantes (nombre_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, grado_ingreso, fecha_inscripcion, recomendado_por, padre_id, madre_id, activo, periodo_id)
                    VALUES (:nombre, :fecha_nac, :lugar_nac, :nacionalidad, :idioma, :direccion, :tel_casa, :tel_movil, :tel_emergencia, :grado, :fecha_insc, :recomendado, :padre_id, :madre_id, :activo, :periodo_id)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre_completo', $nombre_completo);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':lugar_nacimiento', $lugar_nacimiento);
        $stmt->bindParam(':nacionalidad', $nacionalidad);
        $stmt->bindParam(':idioma', $idioma);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono_casa', $telefono_casa);
        $stmt->bindParam(':telefono_movil', $telefono_movil);
        $stmt->bindParam(':telefono_emergencia', $telefono_emergencia);
        $stmt->bindParam(':grado_ingreso', $grado_ingreso);
        $stmt->bindParam(':fecha_inscripcion', $fecha_inscripcion);
        $stmt->bindParam(':recomendado_por', $recomendado_por);
        $stmt->bindParam(':padre_id', $padre_id);
        $stmt->bindParam(':madre_id', $madre_id);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
        $stmt->bindParam(':periodo_id', $periodo_id);
        $stmt->execute();

        $mensaje = "✅ Estudiante registrado exitosamente.";
    } catch (PDOException $e) {
        $mensaje = "❌ Error en el registro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla de Inscripción - CEIA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .formulario h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .content {
            text-align: center;
            margin-top: 100px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 200px;
            margin-bottom: 20px;

        }

        .alerta {
            background: #ddffdd;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid green;
        }

        .alerta-error {
            background: #ffdddd;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid red;
        }
    </style>
</head>
<body style="background-image: url('img/fondo.jpg'); background-size: cover;">
    <?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>PLANILLA DE INSCRIPCIÓN</h1></br>
    </div>

    <div class="formulario">
        <h2>Formulario de Inscripción de Estudiantes</h2>

        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label>Nombre completo del estudiante:</label>
            <input type="text" name="nombre_completo" required>

            <label>Fecha de nacimiento:</label>
            <input type="date" name="fecha_nacimiento" required>

            <label>Lugar de nacimiento:</label>
            <input type="text" name="lugar_nacimiento" required>

            <label>Nacionalidad:</label>
            <input type="text" name="nacionalidad" required>

            <label>Idioma:</label>
            <input type="text" name="idioma" required>

            <label>Direccion:</label>
            <input type="text" name="direccion" required>

            <label>Telefono de casa:</label>
            <input type="text" name="telefono_casa" required>

            <label>Telefono móvil:</label>
            <input type="text" name="telefono_movil" required>

            <label>Telefono de emergencia:</label>
            <input type="text" name="telefono_emergencia" required>

            <label>Grado al que se inscribe:</label>
            <input type="text" name="grado_ingreso" required>

            <label>Fecha de inscripción:</label>
            <input type="date" name="fecha_inscripcion" required>

            <label>Recomendado por:</label>
            <input type="text" name="recomendado_por" required>

            <label><input type="checkbox" name="activo"> Estudiante Activo</label><br><br>

            <button type="submit">Guardar Inscripción</button>
        </form>
    </div>
</body>
</html>