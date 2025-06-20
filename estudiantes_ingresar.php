<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require_once "conn/conexion.php";

// Agregar estudiante
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre = $_POST["nombre"];
    $fecha_nac = $_POST["fecha_nacimiento"];
    $lugar_nac = $_POST["lugar_nacimiento"];
    $nacionalidad = $_POST["nacionalidad"];
    $idioma = $_POST["idioma"];
    $direccion = $_POST["direccion"];
    $tel_casa = $_POST["tel_casa"];
    $tel_movil = $_POST["tel_movil"];
    $tel_emergencia = $_POST["tel_emergencia"];
    $grado = $_POST["grado"];
    $fecha_insc = $_POST["fecha_inscripcion"];
    $recomendado_por = $_POST["recomendado_por"];

    // Validar si ya existe
    $check = $conn->prepare("SELECT id FROM estudiantes WHERE nombre_completo = :nombre AND fecha_nacimiento = :fecha");
    $check->execute([':nombre' => $nombre, ':fecha' => $fecha_nac]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ El estudiante ya está registrado.";
    } else {
        $insert = $conn->prepare("INSERT INTO estudiantes (nombre_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, grado_ingreso, fecha_inscripcion, recomendado_por) VALUES (:nombre, :fecha, :lugar, :nacionalidad, :idioma, :direccion, :tel_casa, :tel_movil, :tel_emergencia, :grado, :fecha_insc, :recomendado)");
        $insert->execute([
            ':nombre' => $nombre,
            ':fecha' => $fecha_nac,
            ':lugar' => $lugar_nac,
            ':nacionalidad' => $nacionalidad,
            ':idioma' => $idioma,
            ':direccion' => $direccion,
            ':tel_casa' => $tel_casa,
            ':tel_movil' => $tel_movil,
            ':tel_emergencia' => $tel_emergencia,
            ':grado' => $grado,
            ':fecha_insc' => $fecha_insc,
            ':recomendado' => $recomendado_por
        ]);
        $mensaje = "✅ Estudiante registrado correctamente.";
    }
}

// Listado estudiantes
$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Estudiantes</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validaciones.js"></script>
</head>
<body>
    <div class="login-box">
        <h2>Registro de Estudiantes</h2>
        <?php if (isset($mensaje)) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST" onsubmit="return validarEstudiante();">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="date" name="fecha_nacimiento" required>
            <input type="text" name="lugar_nacimiento" placeholder="Lugar de nacimiento">
            <input type="text" name="nacionalidad" placeholder="Nacionalidad">
            <input type="text" name="idioma" placeholder="Idiomas que habla">
            <textarea name="direccion" placeholder="Dirección del estudiante"></textarea>
            <input type="text" name="tel_casa" placeholder="Teléfono de casa">
            <input type="text" name="tel_movil" placeholder="Teléfono celular">
            <input type="text" name="tel_emergencia" placeholder="Teléfono de emergencia">
            <input type="text" name="grado" placeholder="Grado de ingreso" required>
            <input type="date" name="fecha_inscripcion" required>
            <input type="text" name="recomendado_por" placeholder="Recomendado por">
            <br>

            <!-- Vínculos a padres/madres -->
            <a href="padres.php" class="boton-link">Registrar Padre</a>
            <a href="madres.php" class="boton-link">Registrar Madre</a>

            <br><br>
            <button type="submit" name="agregar">Agregar Estudiante</button>
        </form>

        <hr>
        <h3>Estudiantes Registrados</h3>
        <ul>
            <?php foreach ($estudiantes as $e): ?>
                <li>
                    <?= htmlspecialchars($e['nombre_completo']) ?> - 
                    <a href="editar_estudiante.php?id=<?= $e['id'] ?>">Editar</a> |
                    <a href="eliminar_estudiante.php?id=<?= $e['id'] ?>" onclick="return confirm('¿Eliminar estudiante?')">Eliminar</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>