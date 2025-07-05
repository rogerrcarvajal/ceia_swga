<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Registrar entrada/salida
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profesor_id = $_POST["profesor_id"];
    $tipo_movimiento = $_POST["tipo_movimiento"];

    $sql = "INSERT INTO entrada_salida_profesores (profesor_id, tipo_movimiento) VALUES (:profesor_id, :tipo_movimiento)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':profesor_id' => $profesor_id, ':tipo_movimiento' => $tipo_movimiento]);

    $mensaje = "✅ Movimiento registrado correctamente.";
}

$profesores = $conn->query("SELECT * FROM profesores ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Profesores</title>
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
        <h2>Control de Entrada y Salida <br>Profesores</br></h2>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <form method="POST">
            <label>Seleccione Profesor:</label><br>
            <select name="profesor_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Movimiento:</label><br>
            <select name="tipo_movimiento" required>
                <option value="Entrada">Entrada</option>
                <option value="Salida">Salida</option>
            </select><br><br>

            <button type="submit">Registrar Movimiento</button>
        </form>
    </div>
</body>
</html>