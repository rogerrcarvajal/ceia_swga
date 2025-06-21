<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}

require_once "conn/conexion.php";
require_once "lib/phpqrcode.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $estudiante_id = $_POST["estudiante_id"];
    $hora_llegada = $_POST["hora_llegada"];

    // Registrar la llegada
    $sql = "INSERT INTO llegadas_tarde (estudiante_id, hora_llegada) VALUES (:estudiante_id, :hora_llegada)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':estudiante_id' => $estudiante_id,
        ':hora_llegada' => $hora_llegada
    ]);

    // Obtener datos del estudiante
    $stmt2 = $conn->prepare("SELECT nombre_completo, grado_ingreso FROM estudiantes WHERE id = :id");
    $stmt2->execute([':id' => $estudiante_id]);
    $est = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Generar contenido QR
    $qrData = "ID: {$estudiante_id}\nNombre: {$est['nombre_completo']}\nGrado: {$est['grado_ingreso']}\nHora: {$hora_llegada}";
    $qrFile = "qrcodes/latepass_{$estudiante_id}_" . time() . ".png";
    QRcode::png($qrData, $qrFile, 'L', 4, 2);

    $mensaje = "✅ QR generado correctamente.";
}

$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);

// Consultar estudiantes que llegaron tarde por grado
$consulta = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['consultar'])) {
    $grado_consulta = $_POST["grado_consulta"];
    $sql = "SELECT l.hora_llegada, e.nombre_completo, e.grado_ingreso 
            FROM llegadas_tarde l 
            JOIN estudiantes e ON l.estudiante_id = e.id 
            WHERE e.grado_ingreso = :grado AND l.hora_llegada > '08:05:00'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':grado' => $grado_consulta]);
    $consulta = $stmt->fetchAll(PDO::FETCH_ASSOC);

    <li>
    <?= htmlspecialchars($c['nombre_completo']) ?> - <?= $c['hora_llegada'] ?>
    <a href="editar_llegada.php?id=<?= $c['id'] ?>">Editar</a> |
    <a href="eliminar_llegada.php?id=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar este registro?')">Eliminar</a>
    </li>
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Late-Pass - CEIA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Registro Late-Pass</h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <form method="POST">
            <label for="estudiante_id">Seleccione Estudiante:</label><br>
            <select name="estudiante_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?> - Grado: <?= $e['grado_ingreso'] ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="hora_llegada">Hora de Llegada:</label><br>
            <input type="time" name="hora_llegada" required><br><br>

            <button type="submit" name="registrar">Registrar Llegada y Generar QR</button>
        </form>

        <br><hr><br>

        <h3>Consulta de Llegadas Tarde por Grado</h3>
        <form method="POST">
            <input type="text" name="grado_consulta" placeholder="Ingrese el grado (Ej: 4to, 5to)" required><br><br>
            <button type="submit" name="consultar">Consultar</button>
        </form>

        <?php if ($consulta): ?>
            <h4>Estudiantes que llegaron tarde:</h4>
            <ul>
                <?php foreach ($consulta as $c): ?>
                    <li><?= htmlspecialchars($c['nombre_completo']) ?> - <?= $c['hora_llegada'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <br>
        <a href="dashboard.php" class="boton-link">Volver al Dashboard</a>
    </div>
</body>
</html>