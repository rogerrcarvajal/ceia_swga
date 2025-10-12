<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}
require_once __DIR__ . '/../src/config.php';

// --- CONTROL DE ACCESO ---
if (!in_array($_SESSION['usuario']['rol'], ['master', 'admin', 'consulta'])) {
    $_SESSION['error_acceso'] = "Acceso denegado.";
    header("Location: /ceia_swga/pages/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SWGA - Reimprimir Autorizaciones</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
        body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .content { text-align: center; color: white; text-shadow: 1px 1px 2px black; margin-top: 20px; padding-top: 10px;}
        .content img { width: 250px; }
        .main-container { display: flex; max-width: 1200px; margin: 20px auto; gap: 20px; background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); padding: 20px; border-radius: 10px; }
        .menu-lateral { flex: 1; }
        .panel-seleccion { flex: 2; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.2); }
        .menu-lateral ul { list-style: none; padding: 0; }
        .menu-lateral li { padding: 12px; margin-bottom: 8px; background-color: rgba(255,255,255,0.1); border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
        .menu-lateral li:hover, .menu-lateral li.active { background-color: rgba(255,255,255,0.3); }
        .form-section { display: none; }
        #select-item { width: 100%; padding: 8px; margin-bottom: 15px; }
        .btn { background-color: rgb(48, 48, 48); color: white; padding: 10px 18px; text-decoration: none; display: inline-block; border-radius: 5px; cursor: pointer; border: none; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
<div class="content">
    <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA";>
    <h1>Reimprimir Autorizaciones de Salida</h1>
</div>

<div class="main-container">
    <div class="menu-lateral">
        <h3>Seleccione Categoría</h3>
        <ul>
            <li data-target="estudiantes">Estudiantes</li>
            <li data-target="staff">Staff</li>
        </ul>
         <a href="/ceia_swga/pages/menu_reportes.php" class="btn">Volver</a>
    </div>

    <div class="panel-seleccion">
        <div id="panel-informativo">
            <p>Seleccione una categoría del menú de la izquierda para empezar.</p>
        </div>

        <div id="form-container" class="form-section">
            <h3 id="form-title"></h3>
            <form id="selection-form">
                <label for="select-item">Seleccione un item:</label>
                <select id="select-item" name="id" required>
                    <!-- Opciones se llenarán con JS -->
                </select>
                <br><br>
                <button type="submit" class="btn">Ver Autorizaciones</button>
            </form>
        </div>
    </div>
</div>

<script src="/ceia_swga/public/js/regenerar_autorizaciones.js"></script>
</body>
</html>