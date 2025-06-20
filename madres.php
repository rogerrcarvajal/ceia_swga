<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Agregar madre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $fecha_nac = $_POST["fecha_nacimiento"];
    $cedula = $_POST["cedula"];
    $nacionalidad = $_POST["nacionalidad"];
    $idiomas = $_POST["idiomas"];
    $profesion = $_POST["profesion"];
    $empresa = $_POST["empresa"];
    $tel_trabajo = $_POST["telefono_trabajo"];
    $celular = $_POST["celular"];
    $email = $_POST["email"];

    $check = $conn->prepare("SELECT id FROM madres WHERE cedula_pasaporte = :cedula");
    $check->execute([':cedula' => $cedula]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Ya existe una madre registrada con esa cédula/pasaporte.";
    } else {
        $sql = "INSERT INTO madres (nombre, apellido, fecha_nacimiento, cedula_pasaporte, nacionalidad, idiomas, profesion, empresa, telefono_trabajo, celular, email) 
                VALUES (:nombre, :apellido, :fecha_nacimiento, :cedula, :nacionalidad, :idiomas, :profesion, :empresa, :telefono_trabajo, :celular, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':fecha_nacimiento' => $fecha_nac,
            ':cedula' => $cedula,
            ':nacionalidad' => $nacionalidad,
            ':idiomas' => $idiomas,
            ':profesion' => $profesion,
            ':empresa' => $empresa,
            ':telefono_trabajo' => $tel_trabajo,
            ':celular' => $celular,
            ':email' => $email
        ]);
        $mensaje = "✅ Madre registrada correctamente.";
    }
}

$madres = $conn->query("SELECT * FROM madres ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Madres</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Registro de Madres</h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="date" name="fecha_nacimiento" required>
            <input type="text" name="cedula" placeholder="Cédula o Pasaporte" required>
            <input type="text" name="nacionalidad" placeholder="Nacionalidad">
            <input type="text" name="idiomas" placeholder="Idiomas">
            <input type="text" name="profesion" placeholder="Profesión">
            <input type="text" name="empresa" placeholder="Empresa donde trabaja">
            <input type="text" name="telefono_trabajo" placeholder="Teléfono trabajo">
            <input type="text" name="celular" placeholder="Celular">
            <input type="email" name="email" placeholder="Correo electrónico">
            <br><br>
            <button type="submit" name="agregar">Agregar Madre</button>
        </form>

        <hr>
        <h3>Madres Registradas</h3>
        <ul>
            <?php foreach ($madres as $m): ?>
                <li>
                    <?= htmlspecialchars($m['nombre'] . " " . $m['apellido']) ?> - <?= htmlspecialchars($m['cedula_pasaporte']) ?>
                    <a href="editar_madre.php?id=<?= $m['id'] ?>">Editar</a> |
                    <a href="eliminar_madre.php?id=<?= $m['id'] ?>" onclick="return confirm('¿Eliminar madre?')">Eliminar</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <br>
        <a href="agregar_estudiante.php" class="boton-link">Volver a Estudiantes</a>
    </div>
</body>
</html>