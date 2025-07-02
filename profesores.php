<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre_completo = $_POST["nombre_completo"] ?? '';
    $cedula = $_POST["cedula"] ?? '';
    $posicion = $_POST["posicion"] ?? '';
    $homeroom_teacher = $_POST["homeroom_teacher"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $email = $_POST["email"] ?? '';

    $check = $conn->prepare("SELECT id FROM profesores WHERE cedula = :cedula");
    $check->execute([':cedula' => $cedula]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Staff / Profesor ya registrado con esta cédula.";
    } else {
        $sql = "INSERT INTO profesores (nombre_completo, cedula, posicion, homeroom_teacher, telefono, email)
                VALUES (:nombre_completo, :cedula, :posicion, :homeroom_teacher, :telefono, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre_completo' => $nombre_completo,
            ':cedula' => $cedula,
            ':posicion' => $posicion,
            ':homeroom_teacher' => $homeroom_teacher,
            ':telefono' => $telefono,
            ':email' => $email
        ]);
        $mensaje = "✅ Staff / Profesor registrado correctamente.";
    }
}

$profesores = $conn->query("SELECT * FROM profesores ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Staff / Profesores</title>
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
            margin: 0px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 65%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
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

        .content {
            text-align: center;
            margin-top: 10px;
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
        <h1><br>Gestión de Staff / Profesores</h1></br>
    </div>

    <div class="formulario-contenedor">
        <div class="form-seccion">
        <h3><br>Registro de Staff / Profesores</h3></br>
        <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>

        <form method="POST">
            <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
            <input type="text" name="cedula" placeholder="Cédula" required>
            <select name="posicion" required>
                <option value="">Posicion / Especialidad</option>
                <option value="Director">Director</option>
                <option value="Bussiness Manager">Bussiness Manager</option>
                <option value="Administrative Assistant">Administrative Assistant</option>
                <option value="IT Manager">IT Manager</option>
                <option value="Psychology">Psychology</option>
                <option value="DC-Grade 12 Music">DC-Grade 12 Music</option>
                <option value="Daycare, Pk-3">Daycare, Pk-3</option>
                <option value="Pk-4, Kindergarten">Pk-4, Kindergarten</option>
                <option value="Grade 1">Grade 1</option>
                <option value="Grade 2">Grade 2</option>
                <option value="Grade 3">Grade 3</option>
                <option value="Grade 4">Grade 4</option>
                <option value="Grade 5">Grade 5</option>
                <option value="Grade 6">Grade 6</option>
                <option value="Grade 7">Grade 7</option>
                <option value="Grade 8">Grade 8</option>
                <option value="Grade 9">Grade 9</option>
                <option value="Grade 10">Grade 10</option>
                <option value="Grade 11">Grade 11</option>
                <option value="Grade 12">Grade 12</option>
                <option value="Spanish teacher - Grade 1-6">Spanish teacher - Grade 1-6</option>
                <option value="Spanish teacher - Grade 7-12">Spanish teacher - Grade 7-12</option>
                <option value="Social Studies - Grade 6-12">Social Studies - Grade 6-12</option>
                <option value="IT Teacher - Grade Pk-3-12">IT Teacher - Grade Pk-3-12</option>
                <option value="Science Teaacher - Grade 6-12">Science Teaacher - Grade 6-12</option>
                <option value="ESL - Elementary">ESL - Elementary</option>
                <option value="ESL - Secondary">ESL - Secondary</option>
                <option value="PE - Grade Pk3-12">PE - Grade Pk3-12</option>
                <option value="Language Arts - Grade 6-9">Language Arts - Grade 6-9</option>
                <option value="Math teacher - Grade 6-9">Math teacher - Grade 6-9</option>
                <option value="Math teacher - Grade 10-12">Math teacher - Grade 10-12</option>
                <option value="Librarian">Librarian</option>
            </select>
            <select name="homeroom_teacher" required>
                <option value="">Homeroom Teacher</option>
                <option value="N/A">N/A</option>
                <option value="Daycare, Pk-3">Daycare, Pk-3</option>
                <option value="Pk-4, Kindergarten">Pk-4, Kindergarten</option>
                <option value="Grade 1">Grade 1</option>
                <option value="Grade 2">Grade 2</option>
                <option value="Grade 3">Grade 3</option>
                <option value="Grade 4">Grade 4</option>
                <option value="Grade 5">Grade 5</option>
                <option value="Grade 6">Grade 6</option>
                <option value="Grade 7">Grade 7</option>
                <option value="Grade 8">Grade 8</option>
                <option value="Grade 9">Grade 9</option>
                <option value="Grade 10">Grade 10</option>
                <option value="Grade 11">Grade 11</option>
                <option value="Grade 12">Grade 12</option>
            </select>
            <input type="text" name="telefono" placeholder="Teléfono de contacto">
            <input type="email" name="email" placeholder="Correo electrónico">
            <br><br>
            <button type="submit" name="agregar">Agregar Profesor</button>
        </form>
        </div> 

        <div class="form-seccion">
            <h3>Staff Registrados</h3>
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
    </div>
</body>
</html>