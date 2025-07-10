
<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: /index.php"); exit();
}
require_once __DIR__ . '/../src/config.php';

// Unimos ambas tablas para tener una sola lista de representantes
$padres_sql = "(SELECT id, padre_nombre as nombre, padre_apellido as apellido, 'padre' as tipo FROM padres)
               UNION
               (SELECT id, madre_nombre as nombre, madre_apellido as apellido, 'madre' as tipo FROM madres)
               ORDER BY apellido, nombre";
$representantes = $conn->query($padres_sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Expedientes de Padres</title>
    <link rel="stylesheet" href="/public/css/estilo_admin.css"> </head>
<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content"><h1>Administrar Expedientes de Padres</h1></div>

    <div class="main-container">
        <div class="left-panel">
            <h3>Lista de Representantes</h3>

    <div class="main-container">
        <div class="left-panel">
            <h3>Lista de Representantes</h3>
            <input type="text" id="filtro_representantes" placeholder="Buscar por apellido...">
            <ul id="lista_representantes">
                <?php foreach ($representantes as $r): ?>
                    <li data-id="<?= $r['id'] ?>" data-tipo="<?= $r['tipo'] ?>">
                        <?= htmlspecialchars($r['apellido'] . ', ' . $r['nombre']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="right-panel">
            <div id="panel_informativo"><p>Seleccione un representante de la lista.</p></div>
            
            <div id="panel_datos_representante" style="display:none;">
                <div id="mensaje_actualizacion" class="mensaje" style="display:none;"></div>
                <div class="form-grid">
                    <form id="form_representante">
                        <h3>Datos del Representante</h3>
                        <input type="hidden" name="id" id="representante_id">
                        <input type="hidden" name="tipo" id="representante_tipo">
                        Nombres: <input type="text" name="nombre" id="rep_nombre" required>
                        Apellidos: <input type="text" name="apellido" id="rep_apellido" required>
                        Fecha Nacimiento: <input type="date" name="fecha_nacimiento" id="rep_fecha_nacimiento">
                        Cédula/Pasaporte: <input type="text" name="cedula_pasaporte" id="rep_cedula_pasaporte">
                        Nacionalidad: <input type="text" name="nacionalidad" id="rep_nacionalidad">
                        Idioma: <input type="text" name="idioma" id="rep_idioma">
                        Profesión: <input type="text" name="profesion" id="rep_profesion">
                        Empresa: <input type="text" name="empresa" id="rep_empresa">
                        Teléfono Trabajo: <input type="text" name="telefono_trabajo" id="rep_telefono_trabajo">
                        Celular: <input type="text" name="celular" id="rep_celular">
                        Email: <input type="email" name="email" id="rep_email">
                        <button type="submit">Actualizar Representante</button>
                    </form>
                    <div class="related-section">
                        <h3>Estudiantes Vinculados</h3>
                        <ul id="lista_estudiantes_vinculados"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/public/js/admin_padres.js"></script>
</body>
</html>