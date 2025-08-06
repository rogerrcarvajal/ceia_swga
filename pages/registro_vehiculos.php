<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';
$mensaje = "";
$accion = $_GET['accion'] ?? '';
$id_editar = $_GET['id'] ?? null;

$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY apellido_completo")->fetchAll(PDO::FETCH_ASSOC);

// Guardar nuevo o editar vehículo
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vehiculo_id = $_POST['vehiculo_id'] ?? null;
    $estudiante_id = $_POST["estudiante_id"];
    $placa = strtoupper(trim($_POST["placa"]));
    $conductor_nombre = ucwords(strtolower(trim($_POST["conductor_nombre"])));

    if ($vehiculo_id) {
        $sql = "UPDATE vehiculos SET estudiante_id = ?, placa = ?, conductor_nombre = ? WHERE id = ?";
        $conn->prepare($sql)->execute([$estudiante_id, $placa, $conductor_nombre, $vehiculo_id]);
        $mensaje = "✅ Vehículo actualizado correctamente.";
    } else {
        $sql = "INSERT INTO vehiculos (estudiante_id, placa, conductor_nombre, autorizado) VALUES (?, ?, ?, true)";
        $conn->prepare($sql)->execute([$estudiante_id, $placa, $conductor_nombre]);
        $mensaje = "✅ Vehículo registrado correctamente.";
    }
}

// Eliminar vehículo
if ($accion === 'eliminar' && $id_editar) {
    $conn->prepare("DELETE FROM vehiculos WHERE id = ?")->execute([$id_editar]);
    $mensaje = "🚫 Vehículo eliminado.";
}

// Obtener vehículo para edición
$vehiculo_editar = null;
if ($accion === 'editar' && $id_editar) {
    $stmt = $conn->prepare("SELECT * FROM vehiculos WHERE id = ?");
    $stmt->execute([$id_editar]);
    $vehiculo_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Vehículos registrados
$vehiculos = $conn->query("
    SELECT v.id, v.placa, v.conductor_nombre, v.estudiante_id,
           e.nombre_completo || ' ' || e.apellido_completo AS estudiante
    FROM vehiculos v
    JOIN estudiantes e ON e.id = v.estudiante_id
    ORDER BY v.placa
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Vehículos Autorizados</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body {
            background-image: url("/ceia_swga/public/img/fondo.jpg");
            background-size: cover;
            color: white;
        }
        .contenedor { display: flex; flex-wrap: wrap; justify-content: space-between; max-width: 95%; margin: auto; padding: 20px; }
        .formulario-contenedor, .tabla-contenedor {
            background-color: rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(227,228,237,0.4);
            padding: 20px;
            margin: 10px;
            flex: 1 1 45%;
            min-width: 400px;
        }
        .formulario-contenedor h2, .tabla-contenedor h2 { text-align: center; margin-bottom: 20px; }
        .alerta { margin: 10px auto; background: rgba(0,0,0,0.6); padding: 10px; border-radius: 8px; text-align: center; color: #a2ff96; font-weight: bold;}
        table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.1); }
        table th, table td { padding: 8px; border: 1px solid rgba(255,255,255,0.2); text-align: left; }
        table th { background-color: rgba(0,0,0,0.3); }
        .acciones a { margin-right: 10px; color: lightblue; text-decoration: none; }
        .btn { margin-top: 15px; display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        .reloj-digital {
            font-size: 1.5rem;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 10px 0;
            text-shadow: 1px 1px 2px black;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="contenedor">

    <!-- Registro de Vehículo -->
    <div class="formulario-contenedor">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo" style="width:150px; display:block; margin:auto;">
        <h2><?= $vehiculo_editar ? 'Editar Vehículo' : 'Registrar Vehículo' ?></h2>

        <?php if ($mensaje): ?>
            <div class="alerta"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="vehiculo_id" value="<?= $vehiculo_editar['id'] ?? '' ?>">

            <label>Estudiante:</label><br>
            <select name="estudiante_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= (isset($vehiculo_editar['estudiante_id']) && $vehiculo_editar['estudiante_id'] == $e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['apellido_completo'] . ', ' . $e['nombre_completo']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="text" name="placa" placeholder="Placa del Vehículo" value="<?= $vehiculo_editar['placa'] ?? '' ?>" required><br><br>
            <input type="text" name="conductor_nombre" placeholder="Nombre del Conductor" value="<?= $vehiculo_editar['conductor_nombre'] ?? '' ?>" required><br><br>

            <button type="submit" class="btn"><?= $vehiculo_editar ? 'Actualizar' : 'Registrar' ?></button>
        </form>
    </div>

    <!-- Tabla de vehículos registrados -->
    <div class="tabla-contenedor">
        <div class="reloj-digital" id="reloj"></div>
        <h2>Vehículos Autorizados</h2>
        <table>
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>Estudiante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($vehiculos as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['placa']) ?></td>
                    <td><?= htmlspecialchars($v['conductor_nombre']) ?></td>
                    <td><?= htmlspecialchars($v['estudiante']) ?></td>
                    <td class="acciones">
                        <a href="?accion=editar&id=<?= $v['id'] ?>">✏️</a>
                        <a href="?accion=eliminar&id=<?= $v['id'] ?>" onclick="return confirm('¿Eliminar este registro?')">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="/ceia_swga/pages/menu_latepass.php" class="btn">Volver al Menú</a>
    </div>
</div>

<script>
    function actualizarReloj() {
        const reloj = document.getElementById('reloj');
        const ahora = new Date();
        reloj.textContent = ahora.toLocaleTimeString('es-VE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();
</script>
</body>
</html>