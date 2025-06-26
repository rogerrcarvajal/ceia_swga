<?php
session_start();
require_once "conn/conexion.php";

$mensaje = "";

// Registrar nuevo período
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear'])) {
    $nombre = $_POST['nombre_periodo'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $sql = "INSERT INTO periodos_escolares (nombre_periodo, fecha_inicio, fecha_fin) VALUES (:nombre, :inicio, :fin)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':nombre' => $nombre, ':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
    $mensaje = "✅ Período escolar creado correctamente.";
}

// Activar período escolar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activar'])) {
    $periodo_id = $_POST['periodo_id'];

    $conn->query("UPDATE periodos_escolares SET activo = FALSE");
    $stmt = $conn->prepare("UPDATE periodos_escolares SET activo = TRUE WHERE id = :id");
    $stmt->execute([':id' => $periodo_id]);

    $mensaje = "✅ Período escolar activado correctamente.";
}

$periodos = $conn->query("SELECT * FROM periodos_escolares ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Períodos Escolares</title>
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
            max-width: 65%;
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

        h3 {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        .act {
            color: green;
        }

        .content {
            text-align: center;
            margin-top: 100px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
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
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Gestión de Períodos Escolares</h1></br>
    </div>
    
    <div class="formulario-contenedor">
        <div class="form-seccion">
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
        <form method="POST">
            <h3>Crear Período Escolar</h3>
            <input type="text" name="nombre_periodo" placeholder="Ej: Agosto 2025 - Junio 2026" required><br><br>
            <label>Fecha de Inicio:</label><br>
            <input type="date" name="fecha_inicio" required><br><br>
            <label>Fecha de Fin:</label><br>
            <input type="date" name="fecha_fin" required><br><br>

            <button type="submit" name="crear">Crear Período</button>
            <br></br>
            <a href="dashboard.php" class="boton-link">Volver al Inicio</a>

        </form>
    </div>

        <div class="form-seccion">
            <h3>Períodos Registrados</h3>
            <ul>
                <?php foreach ($periodos as $p): ?>
                    <li class="act">
                        <?= htmlspecialchars($p['nombre_periodo']) ?> <?= $p['activo'] ? "(Activo)": "" ?>
                        <?php if (!$p['activo']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="periodo_id" value="<?= $p['id'] ?>">
                                <button type="submit" name="activar">Activar</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>