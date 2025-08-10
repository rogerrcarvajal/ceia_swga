<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

require_once __DIR__ . '/../src/config.php';

$mensaje = $_SESSION['mensaje_vehiculo'] ?? "";
if (isset($_SESSION['mensaje_vehiculo'])) {
    unset($_SESSION['mensaje_vehiculo']);
}

// --- ESTE ES EL BLOQUE DE CONTROL DE ACCESO ---
// Consulta a la base de datos para verificar si hay algún usuario con rol 'admin'
if (!isset($_SESSION['usuario']['rol']) || !in_array($_SESSION['usuario']['rol'], ['master','admin'])) {
    $_SESSION['error_acceso'] = "Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes.";
    echo '<script>window.onload = function() { alert("Acceso denegado. Solo usuarios autorizados pueden gestionar el módulo de reportes."); window.location.href = "/ceia_swga/pages/dashboard.php"; };</script>';
    exit();
}

// Verificación de periodo activo
$periodo_activo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar los datos del formulario
    if (empty($_POST["estudiante_id"]) || !is_numeric($_POST["estudiante_id"])) {
        $mensaje = "⚠️ Debe seleccionar un estudiante válido.";
    } elseif (empty($_POST["placa"])) {
        $mensaje = "⚠️ La placa es obligatoria.";
    } elseif (empty($_POST["modelo"])) {
        $mensaje = "⚠️ El modelo es obligatorio.";
    } else {

    $estudiante_id = $_POST["estudiante_id"];
    $placa = strtoupper(trim($_POST["placa"]));
    $modelo = trim($_POST["modelo"]);
    $autorizado = isset($_POST["autorizado"]) ? true : false;

    // Verificar si la placa ya existe
        $check_stmt = $conn->prepare("SELECT id FROM vehiculos WHERE placa = :placa");
        $check_stmt->execute([':placa' => $placa]);
    if ($check_stmt->rowCount() > 0) {
        $mensaje = "⚠️ Ya existe un vehículo registrado con esta placa.";
    } else {
        $stmt = $conn->prepare("INSERT INTO vehiculos (estudiante_id, placa, modelo, autorizado) VALUES (:estudiante_id, :placa, :modelo, :autorizado)");
    $stmt->execute([
        ':estudiante_id' => $estudiante_id,
        ':placa' => $placa,
        ':modelo' => $modelo,
        ':autorizado' => $autorizado,
    ]);

        $mensaje = "✅ Vehículo registrado correctamente.";
    }
    }
}

// Obtener estudiantes

$resultados_por_pagina = 10; // Número de resultados por página

$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Registro de Vehículos</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 30px; padding-top: 20px;}
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content img { width: 180px; }
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 30px; border-radius: 10px; max-width: 80%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .form-seccion { width: 45%; color: white; min-width: 350px; }
        .formulario-contenedor h2 { text-align: center; }
        .form-seccion h3 { text-align: center; border-bottom: 1px solidrgb(42, 42, 42); padding-bottom: 10px; }
        .lista-vehiculos { list-style: none; padding: 0; max-height: 400px; overflow-y: auto; }
        .lista-vehiculos li { background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .lista-vehiculos a { color: #87cefa; text-decoration: none; margin-left: 10px; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>

<div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Gestión de Vehículos autorizados</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

   <div class="formulario-contenedor">
        <div class="form-seccion">
            <h3>Registrar Nuevo Vehículo</h3>
            <?php if ($mensaje): ?>
                <div class="alerta"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="POST">
                <label for="estudiante_id">Estudiante:</label>
                <select name="estudiante_id" id="estudiante_id" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($estudiantes as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <input type="text" name="placa" placeholder="Placa del Vehículo" id="placa" required><br><br>

                <input type="text" name="modelo" placeholder="Modelo del Vehículo" id="modelo" required><br><br>

                <label><input type="checkbox" name="autorizado" checked> Autorizado</label><br><br>

                <button type="submit">Registrar Vehículo</button>
                <!-- Botón para volver aal menu de estudiantes -->
                <a href="/ceia_swga/pages/menu_estudiantes.php" class="btn">Volver</a> 

            </form>
        </div>

        <div class="form-seccion">
            <h3>Vehículos Autorizados</h3>
            <ul class="lista-vehiculos">
                <?php
                 // Configuración de la paginación
                $pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $offset = ($pagina_actual - 1) * $resultados_por_pagina;

                // Obtener el número total de vehículos
                $total_vehiculos = $conn->query("SELECT COUNT(*) FROM vehiculos")->fetchColumn();
                $total_paginas = ceil($total_vehiculos / $resultados_por_pagina);

                // Obtener vehículos autorizados con paginación
                $sql_vehiculos = "SELECT v.id, v.placa, v.modelo, v.autorizado, e.nombre_completo AS estudiante 
                                FROM vehiculos v 
                                INNER JOIN estudiantes e ON v.estudiante_id = e.id 
                                ORDER BY e.nombre_completo 
                                LIMIT :limit OFFSET :offset";
                $stmt_vehiculos = $conn->prepare($sql_vehiculos);
                $stmt_vehiculos->bindValue(':limit', $resultados_por_pagina, PDO::PARAM_INT);
    $stmt_vehiculos->bindValue(':offset', $offset, PDO::PARAM_INT);
                $vehiculos = $conn->query("SELECT v.id, v.placa, v.modelo, v.autorizado, e.nombre_completo AS estudiante FROM vehiculos v INNER JOIN estudiantes e ON v.estudiante_id = e.id ORDER BY e.nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
                if (empty($vehiculos)): ?>
                    <li>No hay vehículos registrados.</li>
                <?php else: ?>
                    <?php foreach ($vehiculos as $v): ?>
                        <li>
                            <span>
                                <?= htmlspecialchars($v['estudiante']) ?> - <?= htmlspecialchars($v['placa']) ?> (<?= htmlspecialchars($v['modelo']) ?>)
                                <?php if ($v['autorizado']): ?>
                                    <br><small style="color:#a2ff96;">Autorizado</small>
                                <?php else: ?>
                                    <br><small style="color:#ffc107;">No Autorizado</small>
                                <?php endif; ?>
                            </span>
                            <div>
                                <a href="/ceia_swga/pages/editar_vehiculo.php?id=<?= $v['id'] ?>">Editar</a> |
                                <a href="/ceia_swga/pages/eliminar_vehiculo.php?id=<?= $v['id'] ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este vehículo?')">Eliminar</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
             <!-- Paginación -->
            <div class="paginacion">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>" <?= ($i == $pagina_actual) ? 'class="active"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <style>
                .paginacion { text-align: center; margin-top: 20px; }
                .paginacion a { display: inline-block; padding: 8px 12px; margin: 0 5px; border: 1px solid #ddd; text-decoration: none; color: white; }
                .paginacion a.active { background-color: rgb(48, 48, 48); color: white; border: 1px solid rgba(255, 255, 255, 0.18); }
            </style>
        </div>
    </div>
</body>
</html>