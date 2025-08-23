<?php
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: /ceia_swga/public/index.php");
    exit();
}

// Incluir configuración para obtener el período activo, aunque no se use directamente en los enlaces
require_once __DIR__ . '/../src/config.php';
$periodo_activo = $conn->query("SELECT nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWGA - Módulo de Ayuda</title>
    <link rel="stylesheet" href="/ceia_swga/public/css/style.css">
    <style>
       body { margin: 0; padding: 0; background-image: url("/ceia_swga/public/img/fondo.jpg"); background-size: cover; background-position: top; font-family: 'Arial', sans-serif; color: white;}
        .formulario-contenedor { background-color: rgba(0, 0, 0, 0.3); backdrop-filter:blur(10px); box-shadow: 0px 0px 10px rgba(227,228,237,0.37); border:2px solid rgba(255,255,255,0.18); margin: 20px auto; padding: 20px; border-radius: 10px; max-width: 60%; display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;}
        .content { color: white; text-align: center; margin-top: 20px; text-shadow: 1px 1px 2px black;}
        .content img { width: 180px;}
        .content h2 { margin-bottom: 25px;}
        .lista-menu { list-style: none; padding: 0; text-align: left; width: 100%;}
        .lista-menu li { background-color: rgba(255, 255, 255, 0.1); margin-bottom: 15px; border-radius: 5px; transition: background-color 0.3s ease;}
        .lista-menu li:hover { background-color: rgba(255, 255, 255, 0.25);}
        .lista-menu a { display: block; padding: 15px; color: white; text-decoration: none; font-size: 1.1em;}
        .lista-menu p { margin: 0; padding: 0 15px 15px 15px; font-size: 0.9em; color: #ccc;}
        .lista-menu .icono-menu { margin-right: 12px; font-size: 1.2em;}
        .submenu { list-style: none; padding-left: 40px; margin-top: -10px; max-height: 0; overflow: hidden; transition: max-height 0.5s ease-out;}
        .submenu.open { max-height: 500px; /* Ajustar si hay más items */}
        .submenu li { background-color: rgba(0,0,0,0.2); margin-bottom: 8px;}
        .submenu a { font-size: 1em; padding: 10px 15px; }
        .submenu p { display: none; }
        .has-submenu > a { cursor: pointer; }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../src/templates/navbar.php'; ?>
    <div class="content">
        <img src="/ceia_swga/public/img/logo_ceia.png" alt="Logo CEIA">
        <h1>Módulo de Ayuda y Soporte</h1>
        <?php if ($periodo_activo): ?>
            <h3 style="color: #a2ff96;">Período Activo: <?= htmlspecialchars($periodo_activo['nombre_periodo']) ?></h3>
        <?php endif; ?>
    </div>

    <div class="formulario-contenedor">
        <div class="content" style="width: 100%;">
            <ul class="lista-menu">
                <li>
                    <a href="/ceia_swga/pages/view_document.php?file=Módulo Ayuda/Funcionalidad_Modulo_Ayuda_Manua_Usuario.md">
                        <span class="icono-menu">📖</span> Manual de Usuario
                    </a>
                    <p>Guía completa sobre el uso y las funcionalidades del sistema.</p>
                </li>

                <li class="has-submenu">
                    <a onclick="toggleSubmenu(this)">
                        <span class="icono-menu">📄</span> Documentación y Funcionalidad del Sistema
                    </a>
                    <p>Explicación técnica detallada de la lógica de negocio de cada módulo y sus componentes.</p>
                    <ul class="submenu">
                        <li><a href="/ceia_swga/pages/view_document.php?file=Módulo Estudiante/Funcionalidad_Modulo_Estudiantes.md">Módulo Estudiante</a></li>
                        <li><a href="/ceia_swga/pages/view_document.php?file=Módulo Staff/Funcionalidad_Modulo_Staff.md">Módulo Staff</a></li>
                        <li><a href="/ceia_swga/pages/view_document.php?file=Módulo Late-Pass/Funcionalidad_Modulo_LatePass.md">Módulo Late-Pass</a></li>
                        <li><a href="/ceia_swga/pages/view_document.php?file=Módulo Reportes/Funcionalidad_Modulo_Reportes.md">Módulo Reportes</a></li>
                        <li><a href="/ceia_swga/pages/view_document.php?file=Módulo Mantenimiento/Funcionalidad_Modulo_Mantenimiento.md">Módulo Mantenimiento</a></li>
                        <li><a href="/ceia_swga/pages/view_document.php?file=Módulo Ayuda/Funcionalidad_Modulo_Ayuda.md">Módulo Ayuda</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Botón para volver al Home -->
            <a href="/ceia_swga/pages/dashboard.php" class="btn" style="margin-top: 20px;">Volver</a> 
        </div>
    </div>

    <script>
        function toggleSubmenu(element) {
            const submenu = element.nextElementSibling.nextElementSibling;
            submenu.classList.toggle('open');
        }
    </script>
</body>
</html>