<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM llegadas_tarde WHERE id = :id");
$stmt->execute([':id' => $id]);
$llegada = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hora_llegada = $_POST["hora_llegada"];

    $sql = "UPDATE llegadas_tarde SET hora_llegada = :hora WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':hora' => $hora_llegada, ':id' => $id]);

    header("Location: latepass.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Llegada Tarde</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Editar Hora de Llegada</h2>
        <form method="POST">
            <label for="hora_llegada">Nueva Hora:</label><br>
            <input type="time" name="hora_llegada" value="<?= $llegada['hora_llegada'] ?>" required><br><br>
            <button type="submit">Actualizar</button>
        </form>
        <br>
        <a href="latepass.php" class="boton-link">Volver</a>
    </div>
</body>
</html>