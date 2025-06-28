<div class="navbar">
    <div class="navbar-logo">CEIA</div>
    <div class="navbar-menu" id="menuToggle">&#9776;</div>
    <div class="navbar-links" id="navLinks">
        <a href="dashboard.php">🏠 Home</a>
            <div class="dropdown">
                <a href="#">📝 Inscripción</a>
                <div class="dropdown-content">
                    <a href="planilla_inscripcion.php">Nuevo Ingreso</a>
                    <a href="administrar_planilla_inscripcion.php">Adminnistrar Planilla de Inscripción</a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#">📝 Late-Pass</a>
                <div class="dropdown-content">
                    <a href="latepass_estudiantes.php">Estudiantes</a>
                    <a href="latepass_profesores.php">Profesores</a>
                </div>
            </div>
            <a href="reportes.php">📊 Reportes</a>
            <div class="dropdown">
                <a href="#">📝 Mantenimiento</a>
                <div class="dropdown-content">
                    <a href="periodos_escolares.php">Períodos Escolares</a>
                    <a href="usuarios.php">Usuarios del Sistema</a>
                    <a href="profesores.php">Profesores</a>
                    <a href="registro_vehiculos.php">Vehículos Autorizados</a>
                </div>
            </div>
        <a href="logout.php" class="logout">Salir</a>
    </div>
</div>

<style>
    .navbar {
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
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
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: rgba(0, 0, 0, 0.9);
        min-width: 160px;
        z-index: 1;
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