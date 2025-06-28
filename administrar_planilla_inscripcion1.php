<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

// Consultar estudiantes
$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);

$mensaje = "";
$datos = null;

// Buscar estudiante
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $estudiante_id = $_POST['estudiante_id'];

    // Consultar datos completos
    $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = :id");
    $stmt->execute([':id' => $estudiante_id]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    $padres = $conn->query("SELECT * FROM padres WHERE id = " . $datos['padre_id'])->fetch(PDO::FETCH_ASSOC);
    $madres = $conn->query("SELECT * FROM madres WHERE id = " . $datos['madre_id'])->fetch(PDO::FETCH_ASSOC);
    $salud = $conn->query("SELECT * FROM salud_estudiantil WHERE estudiante_id = " . $estudiante_id)->fetch(PDO::FETCH_ASSOC);
}

// Actualizar datos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    try {
        $conn->beginTransaction();

        // Actualizar estudiante
        $sql = "UPDATE estudiantes SET nombre_completo = :nombre, direccion = :direccion, telefono_casa = :tel_casa, telefono_movil = :tel_movil, telefono_emergencia = :tel_emergencia, grado_ingreso = :grado, activo = :activo WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $_POST['nombre_completo'],
            ':direccion' => $_POST['direccion'],
            ':tel_casa' => $_POST['telefono_casa'],
            ':tel_movil' => $_POST['telefono_movil'],
            ':tel_emergencia' => $_POST['telefono_emergencia'],
            ':grado' => $_POST['grado_ingreso'],
            ':activo' => isset($_POST['activo']) ? 1 : 0,
            ':id' => $_POST['estudiante_id']
        ]);

        // Actualizar padre
        $sql = "UPDATE padres SET nombre = :nombre, apellido = :apellido, celular = :celular, email = :email WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $_POST['padre_nombre'],
            ':apellido' => $_POST['padre_apellido'],
            ':celular' => $_POST['padre_celular'],
            ':email' => $_POST['padre_email'],
            ':id' => $_POST['padre_id']
        ]);

        // Actualizar madre
        $sql = "UPDATE madres SET nombre = :nombre, apellido = :apellido, celular = :celular, email = :email WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $_POST['madre_nombre'],
            ':apellido' => $_POST['madre_apellido'],
            ':celular' => $_POST['madre_celular'],
            ':email' => $_POST['madre_email'],
            ':id' => $_POST['madre_id']
        ]);

        // Actualizar salud
        $sql = "UPDATE salud_estudiantil SET contacto_emergencia = :contacto, telefono1 = :telefono WHERE estudiante_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':contacto' => $_POST['contacto_emergencia'],
            ':telefono' => $_POST['telefono_emergencia1'],
            ':id' => $_POST['estudiante_id']
        ]);

        $conn->commit();
        $mensaje = "✅ Datos actualizados correctamente.";
    } catch (Exception $e) {
        $conn->rollBack();
        $mensaje = "❌ Error al actualizar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Planilla Inscripción</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-section { background: rgba(0, 0, 0, 7); padding: 20px; border-radius: 10px; margin: 10px; }
        .form-section h3 { margin-bottom: 10px; }
        input, textarea { width: 95%; padding: 5px; margin-bottom: 10px; }
        .boton-link { margin-top: 15px; display: inline-block; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="login-box">
        <h2>Administrar Planilla de Inscripción</h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <!-- Formulario de búsqueda -->
        <form method="POST">
            <label>Seleccione Estudiante:</label><br>
            <select name="estudiante_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= (isset($datos) && $datos['id'] == $e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre_completo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="buscar">Buscar</button>
        </form>

        <?php if ($datos): ?>
            <!-- Formulario de edición -->
            <form method="POST">
                <input type="hidden" name="estudiante_id" value="<?= $datos['id'] ?>">
                <input type="hidden" name="padre_id" value="<?= $datos['padre_id'] ?>">
                <input type="hidden" name="madre_id" value="<?= $datos['madre_id'] ?>">

                <div class="form-section">
                    <h3>Datos del Estudiante</h3>
                    <input type="text" name="nombre_completo" value="<?= $datos['nombre_completo'] ?>" required>
                    <textarea name="direccion"><?= $datos['direccion'] ?></textarea>
                    <input type="text" name="telefono_casa" value="<?= $datos['telefono_casa'] ?>">
                    <input type="text" name="telefono_movil" value="<?= $datos['telefono_movil'] ?>">
                    <input type="text" name="telefono_emergencia" value="<?= $datos['telefono_emergencia'] ?>">
                    <input type="text" name="grado_ingreso" value="<?= $datos['grado_ingreso'] ?>" required>
                    <label><input type="checkbox" name="activo" <?= $datos['activo'] ? 'checked' : '' ?>> Estudiante Activo</label>
                </div>

                <div class="form-section">
                    <h3>Datos del Padre</h3>
                    <input type="text" name="padre_nombre" value="<?= $padre['nombre'] ?>" required>
                    <input type="text" name="padre_apellido" value="<?= $padre['apellido'] ?>" required>
                    <input type="text" name="padre_celular" value="<?= $padre['celular'] ?>">
                    <input type="email" name="padre_email" value="<?= $padre['email'] ?>">
                </div>

                <div class="form-section">
                    <h3>Datos de la Madre</h3>
                    <input type="text" name="madre_nombre" value="<?= $madre['nombre'] ?>" required>
                    <input type="text" name="madre_apellido" value="<?= $madre['apellido'] ?>" required>
                    <input type="text" name="madre_celular" value="<?= $madre['celular'] ?>">
                    <input type="email" name="madre_email" value="<?= $madre['email'] ?>">
                </div>

                <div class="form-section">
                    <h3>Información Médica</h3>
                    <input type="text" name="contacto_emergencia" value="<?= $salud['contacto_emergencia'] ?>" required>
                    <input type="text" name="telefono_emergencia1" value="<?= $salud['telefono1'] ?>" required>
                </div>

                <button type="submit" name="actualizar">Actualizar</button>
                <br><br>
                <a href="control_vehiculos.php?estudiante_id=<?= $datos['id'] ?>" class="boton-link">Registrar Vehículos</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>