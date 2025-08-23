<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

$mensaje = "";

// Roles permitidos
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master', 'consulta'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Late-Pass - Gestión y control</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("/ceia_swga/public/img/fondo.jpg");
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
            color: white;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0px 0px 10px rgba(227, 228, 237, 0.37);
            border: 2px solid rgba(255, 255, 255, 0.18);
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 50%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }

        .content {
            color: white;
            text-align: center;
<<<<<<< HEAD
            margin-top: 20px;
=======
            margin-top: 1px;
>>>>>>> 8d1a461c063b6cdee4cbf4e0693b92c4894df3ad
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 180px;
        }

        .content h2 {
            margin-bottom: 25px;
        }

        .lista-menu {
            backdrop-filter: blur(10px);
            box-shadow: 0px 0px 10px rgba(227, 228, 237, 0.37);
            border: 2px solid rgba(255, 255, 255, 0.18);
            list-style: none;
            padding: 0;
            text-align: left;
        }

        .lista-menu li {
            background-color: rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .lista-menu li:hover {
            background-color: rgba(255, 255, 255, 0.25);
        }

        .lista-menu a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 1.1em;
        }

        .lista-menu p {
            margin: 0;
            padding: 0 15px 15px 15px;
            font-size: 0.9em;
            color: #ccc;
        }

        .lista-menu .icono-reporte {
            margin-right: 12px;
            font-size: 1.2em;
        }

        .btn {
            background-color: rgb(48, 48, 48);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>

<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>

<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
    <h1>Gestión de control de Late-Pass</h1>
    <?php if ($periodo_activo): ?>
        <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
    <?php endif; ?>
</div>

<div class="formulario-contenedor">
    <div class="content">
        <ul class="lista-menu">

            <li>
                <a href="/ceia_swga/pages/generar_qr.php">
                    <span class="icono-reporte">📷</span> Generar Códigos QR
                </a>
                <p>Permite la selección de un estudiante, staff o vehículo para generar su código QR.</p>
            </li>

            <li>
                <a href="/ceia_swga/pages/control_acceso.php">
                    <span class="icono-reporte">✅</span> Control de acceso (Late-Pass)
                </a>
                <p>Escanea el código QR del estudiante, staff o vehículo autorizado para registrar su llegada.</p>
            </li>

            <li>
                <a href="/ceia_swga/pages/gestion_latepass.php">
                    <span class="icono-reporte">📋</span> Gestión y consulta de Late-Pass
                </a>
                <p>Consulta histórica de entradas tarde por estudiante y grado.</p>
            </li>

            <li>
                <a href="/ceia_swga/pages/gestion_es_staff.php">
                    <span class="icono-reporte">📋</span> Gestión y consulta de Entrada/Salida Staff
                </a>
                <p>Consulta los movimientos del personal por fecha y hora.</p>
            </li>

            <li>
                <a href="/ceia_swga/pages/gestion_vehiculos.php">
                    <span class="icono-reporte">🚗</span> Gestión y consulta de Entrada/Salida Vehículos
                </a>
                <p>Consulta los movimientos de vehículos autorizados, hora de entrada y salida del colegio.</p>
            </li>

        </ul>

        <a href="/ceia_swga/pages/dashboard.php" class="btn">Volver</a>
    </div>
</div>
</body>
</html>