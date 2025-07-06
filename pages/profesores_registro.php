<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header(header: "Location: /../public/index.php");
    exit();
}

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Verificar si el rol del usuario NO es 'admin'
if ($_SESSION['usuario']['rol'] !== 'admin') {
    // Guardar un mensaje de error en la sesión para mostrarlo en el dashboard
    $_SESSION['error_mensaje'] = "Acceso denegado. No tiene permiso para ver esta página.";
    header("Location: /../pages/dashboard.php"); // Redirigir a una página segura
    exit();
}

// Incluir configuración y conexión a la base de datos
require_once __DIR__ . '/../src/config.php';

$mensaje = "";

// Obtener período escolar activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear uno.");
}

// Lógica para agregar un nuevo profesor (solo datos básicos)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre_completo = $_POST["nombre_completo"] ?? '';
    $cedula = $_POST["cedula"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $email = $_POST["email"] ?? '';

    // Verificar si la cédula ya existe
    $check = $conn->prepare("SELECT id FROM profesores WHERE cedula = :cedula");
    $check->execute([':cedula' => $cedula]);

    if ($check->rowCount() > 0) {
        $mensaje = "⚠️ Un miembro del staff ya está registrado con esta cédula.";
    } else {
        // La tabla 'profesores' ahora solo contiene estos campos
        $sql = "INSERT INTO profesores (nombre_completo, cedula, telefono, email)
                VALUES (:nombre_completo, :cedula, :telefono, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre_completo' => $nombre_completo,
            ':cedula' => $cedula,
            ':telefono' => $telefono,
            ':email' => $email
        ]);
        $mensaje = "✅ Staff / Profesor registrado correctamente.";
    }
}

// Obtener la lista de profesores para mostrarla
$profesores = $conn->query("SELECT * FROM profesores ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Staff / Profesores</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url('/public/img/fondo.jpg'); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.75); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solid #0057A0; padding-bottom: 10px; }
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; padding-top: 20px;}
        .content img { width: 150px; }
        .lista-profesores { list-style: none; padding: 0; max-height: 400px; overflow-y: auto; }
        .lista-profesores li { background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .lista-profesores a { color: #87cefa; text-decoration: none; margin-left: 10px; }
    </style>    
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Staff / Profesores</h1>
    </div>

    <div class="formulario-contenedor">
        <div class="form-seccion">
            <h3>Registrar Nuevo Ingreso</h3>
            <?php if ($mensaje) echo "<p class='alerta'>$mensaje</p>"; ?>
            <form method="POST">
                <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
                <input type="text" name="cedula" placeholder="Cédula (sin puntos ni comas)" required>
                <input type="text" name="telefono" placeholder="Teléfono de contacto">
                <input type="email" name="email" placeholder="Correo electrónico">
                <br><br>
                <button type="submit" name="agregar">Agregar Staff / Profesor</button>
                <a href="/../pages/profesores_administrar.php" class="boton-link">Administrar Staff / Profesor</a>
            </form>
        </div> 

        <div class="form-seccion">
            <h3>Personal Registrado</h3>
            <ul class="lista-profesores">
                <?php if (empty($profesores)): ?>
                    <li>No hay personal registrado.</li>
                <?php else: ?>
                    <?php foreach ($profesores as $p): ?>
                        <li>
                            <span><?= htmlspecialchars($p['nombre_completo']) ?> (C.I: <?= htmlspecialchars($p['cedula']) ?>)</span>
                            <div>
                                <a href="/pages/editar_profesor.php?id=<?= $p['id'] ?>">Editar</a> |
                                <a href="/pages/eliminar_profesor.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar a este miembro del staff? Se eliminarán todas sus asignaciones en todos los períodos escolares.')">Eliminar</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>
