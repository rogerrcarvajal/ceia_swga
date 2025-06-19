<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre = $_POST["nombre"];
    $cedula = $_POST["cedula"];
    $especialidad = $_POST["especialidad"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];

    $check = $conn->prepare("SELECT id FROM profesores WHERE cedula = :cedula");
    $check->execute([':cedula' => $cedula]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Profesor ya registrado con esta cédula.";
    } else {
        $sql = "INSERT INTO profesores (nombre_completo, cedula, especialidad, telefono, email)
                VALUES (:nombre, :cedula, :especialidad, :telefono, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':cedula' => $cedula,
            ':especialidad' => $especialidad,
            ':telefono' => $telefono,
            ':email' => $email
        ]);
        $mensaje = "✅ Profesor registrado correctamente.";
    }
}

$profesores = $conn->query("SELECT * FROM profesores ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Profesores</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Registro de Profesores</h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="text" name="cedula" placeholder="Cédula" required>
            <input type="text" name="especialidad" placeholder="Especialidad" required>
            <input type="text" name="telefono" placeholder="Teléfono de contacto">
            <input type="email" name="email" placeholder="Correo electrónico">
            <br><br>
            <button type="submit" name="agregar">Agregar Profesor</button>
        </form>

        <hr>
        <h3>Profesores Registrados</h3>
        <ul>
            <?php foreach ($profesores as $p): ?>
                <li>
                    <?= htmlspecialchars($p['nombre_completo']) ?> - <?= htmlspecialchars($p['cedula']) ?>
                    <a href="editar_profesor.php?id=<?= $p['id'] ?>">Editar</a> |
                    <a href="eliminar_profesor.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar profesor?')">Eliminar</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <br>
        <a href="dashboard.php" class="boton-link">Volver al Inicio</a>
    </div>
</body>
</html>