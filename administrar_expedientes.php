<?php
session_start();
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
    $salud_estudiantil = $conn->query("SELECT * FROM salud_estudiantil WHERE estudiante_id = " . $estudiante_id)->fetch(PDO::FETCH_ASSOC);
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
    <title>Administrar Expedientes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
         body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        
        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 0px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 30%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        .form-seccion {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        .content {
            text-align: center;
            margin-top: 10px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 150px;
            margin-bottom: 20px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }   
    </style> 
</head>
<body>
    <?php include 'navbar.php'; ?>
    <br></br>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1>Administrar Expedientes</h1>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <h2>Búsqueda Rápida de Estudiantes</h2>
            <input type="text" id="buscar_estudiante" placeholder="Escriba el nombre del estudiante...">
            <ul id="resultados_estudiantes"></ul>

            <script src="js/buscador.js"></script>

        <?php if ($datos): ?>
            <!-- Formulario de edición -->
            <form method="POST">
                <input type="hidden" name="id" value="<?= $datos['id'] ?>">
                <input type="hidden" name="padre_id" value="<?= $datos['padre_id'] ?>">
                <input type="hidden" name="madre_id" value="<?= $datos['madre_id'] ?>">

                <div class="form-section">
                    <h3>Datos del Estudiante</h3>
                    <input type="text" name="nombre_completo" value="<?= $datos['nombre_completo'] ?>" required>
                    <input type="text" name="apellido_completo" value="<?= $datos['apellido_completo'] ?>" required>
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
                <a href="registro_vehiculos.php?estudiante_id=<?= $datos['id'] ?>" class="boton-link">Registrar Vehículos</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>