<div class="navbar">
    <div class="navbar-logo">CEIA</div>
    <div class="navbar-menu" id="menuToggle">&#9776;</div>
    <div class="navbar-links" id="navLinks">
        <a href="dashboard.php">🏠 Home</a>
            <div class="dropdown">
                <a href="#">📝 Estudiantes</a>
                <div class="dropdown-content">
                    <a href= "/pages/planilla_inscripcion.php">Planilla de Inscripción</a>
                    <a href= "/pages/administrar_planilla_inscripcion.php">Adminnistrar Planilla de Inscripción</a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#">📝 Staff / Profesores</a>
                <div class="dropdown-content">
                    <a href= "/pages/profesores_registro.php">Nuevo Ingreso</a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#">📝 Late-Pass</a>
                <div class="dropdown-content">
                    <a href= "/pages/latepass_estudiantes.php">Estudiantes</a>
                    <a href= "/pages/latepass_profesores.php">Profesores</a>
                </div>
            </div>
            <a href="/pages/reportes_menu.php">📊 Reportes</a>
            <div class="dropdown">
                <a href="#">📝 Mantenimiento</a>
                <div class="dropdown-content">
                    <a href= "/pages/periodos_escolares.php">Períodos Escolares</a>
                    <a href= "/pages/usuarios_configurar.php">Usuarios del Sistema</a>
                </div>
            </div>
        <a href= "/pages/logout.php" class="logout">Salir</a>
    </div>
    </div>
    </div>
</div>

<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        z-index: 1000; /* Asegurarse de que esté por encima de todo */
        background-color: rgba(0, 0, 0, 0.3);
        backdrop-filter:blur(10px);
        box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
        border:2px solid rgba(255,255,255,0.18);
    }

    .navbar-logo {
        color: white;
        font-weight: bold;
        font-size: 22px;
    }

    .navbar-menu {
        display: none;
        font-size: 30px;
        color: white;
        cursor: pointer;
    }

    .navbar-links a {
        color: white;
        text-decoration: none;
        padding: 14px 20px;
        display: inline-block;
        font-weight: bold;
    }

    .navbar-links a:hover {
        background-color: rgba(0, 0, 0, 0.20);
        border-radius: 4px;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        min-width: 160px;
        z-index: 1;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter:blur(10px);
        box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
        border:2px solid rgba(255,255,255,0.18);
    }

    .dropdown-content a {
        display: block;
        padding: 12px 16px;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .logout {
        background-color: red;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .logout:hover {
        background-color: darkred;
    }

    @media (max-width: 768px) {
        .navbar-links {
            display: none;
            flex-direction: column;
            width: 100%;
        }

        .navbar-links.active {
            display: flex;
        }

        .navbar-menu {
            display: block;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    });
</script>


<?php
// Comprobar si existe el mensaje de error en la sesións

if (isset($_SESSION['error_acceso']) && !empty($_SESSION['error_acceso'])):
?>

<style>
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.3);
        backdrop-filter:blur(37px);
        box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
        border:2px solid rgba(255,255,255,0.18);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000; /* Asegurarse de que esté por encima de todo */
    }

    .modal-content {
        background-color: #fff;
        color: #333;
        padding: 30px;
        border-radius: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter:blur(37px);
        box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
        border:2px solid rgba(255,255,255,0.18);
        text-align: center;
        max-width: 500px;
        width: 90%;
    }

    .modal-content h2 {
        color: #d9534f; /* Rojo de advertencia */
        margin-top: 0;
    }

    .modal-content p {
        font-size: 1.1em;
        margin: 20px 0;
    }

    .modal-content .boton-modal {
        display: inline-block;
        padding: 12px 25px;
        background-color:rgb(48, 48, 48);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    
    .modal-content .boton-modal:hover {
        background-color:rgb(48, 48, 48);
    }
</style>

<div class="modal-overlay" id="periodo-modal">
    <div class="modal-content">
        <h2>⚠️ Atención</h2>
        <p>
            <?php 
                // Imprimir el mensaje de error guardado en la sesión
                echo htmlspecialchars(string: $_SESSION['error_acceso']); 
            ?>
        </p>
        <a href="/pages/dashboard.php" class="boton-modal">
            Ir a la pantalla de inicio
        </a>
    </div>
</div>

<?php
    // Limpiar la variable de sesión después de mostrar el mensaje
    // para que no aparezca en otras páginas.
    unset($_SESSION['error_acceso']);
endif;


if (isset($_SESSION['error_periodo_inactivo']) && !empty($_SESSION['error_periodo_inactivo'])):
?>

<style>
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter:blur(37px);
        box-shadow: 0px 0px 10px rgba(227,228,237,0.37);
        border:2px solid rgba(255,255,255,0.18);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000; /* Asegurarse de que esté por encima de todo */
    }

    .modal-content {
        background-color: #fff;
        color: #333;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        text-align: center;
        max-width: 500px;
        width: 90%;
    }

    .modal-content h2 {
        color: #d9534f; /* Rojo de advertencia */
        margin-top: 0;
    }

    .modal-content p {
        font-size: 1.1em;
        margin: 20px 0;
    }

    .modal-content .boton-modal {
        display: inline-block;
        padding: 12px 25px;
        background-color:rgb(48, 48, 48);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    
    .modal-content .boton-modal:hover {
        background-color:rgb(48, 48, 48);
    }
</style>

<div class="modal-overlay" id="periodo-modal">
    <div class="modal-content">
        <h2>⚠️ Atención</h2>
        <p>
            <?php 
                // Imprimir el mensaje de error guardado en la sesión
                echo htmlspecialchars(string: $_SESSION['error_periodo_inactivo']); 
            ?>
        </p>
        <a href="/pages/periodos_escolares.php" class="boton-modal">
            Ir a Gestión de Períodos
        </a>
    </div>
</div>

<?php
    // Limpiar la variable de sesión después de mostrar el mensaje
    // para que no aparezca en otras páginas.
    unset($_SESSION['error_periodo_inactivo']);
endif;
?>