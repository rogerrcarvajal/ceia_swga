<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estudiante_id = $_POST["estudiante_id"];
    $placa = $_POST["placa"];
    $conductor_nombre = $_POST["conductor_nombre"];

    $sql = "INSERT INTO vehiculos_autorizados (estudiante_id, placa, conductor_nombre) VALUES (:estudiante_id, :placa, :conductor_nombre)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':estudiante_id' => $estudiante_id, ':placa' => $placa, ':conductor_nombre' => $conductor_nombre]);

    $mensaje = "✅ Vehículo registrado correctamente.";
}

$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Vehículos Autorizados</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 30%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .content {
            text-align: center;
            margin-top: 10px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 150px;
            margin-bottom: 0px;
        }
    </style>    
</head>
<body>
    <?php include 'navbar.php'; ?>
    <br></br>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h2>Control de Vehículos Autorizados</h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <form method="POST">
            <label>Seleccione Estudiante:</label><br>
            <select name="estudiante_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="text" name="placa" placeholder="Placa del Vehículo" required><br><br>
            <input type="text" name="conductor_nombre" placeholder="Nombre del Conductor" required><br><br>

            <button type="submit">Registrar Vehículo</button>
        </form>
    </div>
</body>
</html>