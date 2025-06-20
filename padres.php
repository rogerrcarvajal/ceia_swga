<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Agregar padre
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

    $check = $conn->prepare("SELECT id FROM padres WHERE cedula_pasaporte = :cedula");
    $check->execute([':cedula' => $cedula]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Ya existe un padre registrado con esa cédula/pasaporte.";
    } else {
        $sql = "INSERT INTO padres (nombre, apellido, fecha_nacimiento, cedula_pasaporte, nacionalidad, idiomas, profesion, empresa, telefono_trabajo, celular, email) 
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
        $mensaje = "✅ Padre registrado correctamente.";
    }
}

$padres = $conn->query("SELECT * FROM padres ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Padres</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Registro de Padres</h2>
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
            <button type="submit" name="agregar">Agregar Padre</button>
        </form>

        <hr>
        <h3>Padres Registrados</h3>
        <ul>
            <?php foreach ($padres as $p): ?>
                <li>
                    <?= htmlspecialchars($p['nombre'] . " " . $p['apellido']) ?> - <?= htmlspecialchars($p['cedula_pasaporte']) ?>
                    <a href="editar_padre.php?id=<?= $p['id'] ?>">Editar</a> |
                    <a href="eliminar_padre.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar padre?')">Eliminar</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <br>
        <a href="agregar_estudiante.php" class="boton-link">Volver a Estudiantes</a>
    </div>
</body>
</html>