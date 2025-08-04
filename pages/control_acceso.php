<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$mensaje = "";

// Verificación de rol autorizado
if (!in_array($_SESSION['usuario']['rol'], ['admin', 'master'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}

// Verificación de período activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$periodo_id = $periodo_activo['id'] ?? null;

if (!$periodo_activo) {
    $_SESSION['error_periodo_inactivo'] = "No hay ningún período escolar activo.";
}

// PROCESAR QR ESCANEADO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_code'])) {
    $qr_id = trim($_POST['qr_code']);
    $usuario = $_SESSION['usuario']['username'];

    if (!ctype_digit($qr_id)) {
        $mensaje = "<div class='alerta error'>Código inválido. Intenta escanear de nuevo.</div>";
    } else {
        // Buscar estudiante
        $stmt = $conn->prepare("SELECT id, nombre_completo FROM estudiantes WHERE id = :id");
        $stmt->execute([':id' => $qr_id]);
        $est = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($est) {
            $hoy = date('Y-m-d');
            $verif_stmt = $conn->prepare("SELECT COUNT(*) FROM latepass WHERE estudiante_id = :id AND fecha = :fecha");
            $verif_stmt->execute([':id' => $qr_id, ':fecha' => $hoy]);
            $ya_registrado = $verif_stmt->fetchColumn();

            if ($ya_registrado) {
                $mensaje = "<div class='alerta advertencia'>El estudiante <strong>{$est['nombre_completo']}</strong> ya fue registrado hoy.</div>";
            } else {
                $inicio_semana = date('Y-m-d', strtotime('monday this week'));
                $fin_semana = date('Y-m-d', strtotime('sunday this week'));
                $strike_stmt = $conn->prepare("SELECT COUNT(*) FROM latepass WHERE estudiante_id = :id AND fecha BETWEEN :ini AND :fin");
                $strike_stmt->execute([':id' => $qr_id, ':ini' => $inicio_semana, ':fin' => $fin_semana]);
                $strikes = (int) $strike_stmt->fetchColumn();

                $observacion = null;
                if ($strikes === 0) {
                    $mensaje = "<div class='alerta advertencia'>Primer strike para <strong>{$est['nombre_completo']}</strong>.</div>";
                } elseif ($strikes === 1) {
                    $mensaje = "<div class='alerta advertencia'>Segundo strike para <strong>{$est['nombre_completo']}</strong>.</div>";
                } else {
                    $mensaje = "<div class='alerta error'>Tercer strike. No puede entrar a la primera hora. Contactar representante.</div>";
                    $observacion = "Tercer strike. No puede entrar a la primera hora. Comunicar al representante.";
                }

                $insert = $conn->prepare("INSERT INTO latepass (estudiante_id, fecha, hora_llegada, periodo_id, observaciones, registrado_por) VALUES (:id, :fecha, :hora, :pid, :obs, :user)");
                $insert->execute([
                    ':id' => $qr_id,
                    ':fecha' => $hoy,
                    ':hora' => date('H:i:s'),
                    ':pid' => $periodo_id,
                    ':obs' => $observacion,
                    ':user' => $usuario
                ]);
            }
        } else {
            // Staff
            $stmt = $conn->prepare("SELECT id FROM profesores WHERE id = :id");
            $stmt->execute([':id' => $qr_id]);
            if ($stmt->fetch()) {
                header("Location: /ceia_swga/pages/registrar_movimiento_staff.php?id=$qr_id");
                exit();
            }

            // Vehículo
            $stmt = $conn->prepare("SELECT id FROM vehiculos WHERE id = :id");
            $stmt->execute([':id' => $qr_id]);
            if ($stmt->fetch()) {
                header("Location: /ceia_swga/pages/registrar_movimiento_vehiculo.php?id=$qr_id");
                exit();
            }

            $mensaje = "<div class='alerta error'>Código no reconocido en el sistema.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SWGA - Control de Acceso</title>
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
            backdrop-filter:blur(10px);
            box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
            border:2px solid rgba(255,255,255,0.18);
            margin: 70px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 80%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }
        .form-seccion {
            width: 45%;
            min-width: 350px;
        }
        .form-seccion h3 {
            text-align: center;
            border-bottom: 1px solid rgb(42, 42, 42);
            padding-bottom: 10px;
        }
        .content {
            text-align: center;
            text-shadow: 1px 1px 2px black;
            margin-top: 30px;
        }
        .content img {
            width: 150px;
        }
        .alerta.exito {
            background-color: rgba(46, 204, 113, 0.8);
            border-left: 5px solid #2ecc71;
        }
        .alerta.advertencia {
            background-color: rgba(241, 196, 15, 0.8);
            border-left: 5px solid #f1c40f;
            color: #333;
        }
        .alerta.error {
            background-color: rgba(231, 76, 60, 0.8);
            border-left: 5px solid #e74c3c;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="formulario-contenedor">
        <div class="content">
            <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
            <h1>Late-Pass - Control de Acceso</h1>
            <?php if ($periodo_activo): ?>
                <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
            <?php endif; ?>
        </div>
        <div class="form-seccion">
            <h3>Escaneo del código QR</h3>
            <p>Coloque el QR frente al lector o cámara</p>
            <form id="qr-form" method="POST">
                <input type="password" id="qr-input" name="qr_code" placeholder="Escanea el QR aquí..." autofocus required>
            </form>
            <?= $mensaje ?>
            <br>
            <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver</a>
        </div>
    </div>
    <script>
        const input = document.getElementById('qr-input');
        input.focus();
        input.addEventListener('change', () => {
            document.getElementById('qr-form').submit();
        });
    </script>
</body>
</html>