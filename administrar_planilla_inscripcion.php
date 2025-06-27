<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$mensaje = "";

// Obtener período escolar activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear uno.");
}

$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Estudiantes CEIA</title>
    <link rel="stylesheet" href="css/estilo_planilla.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: top;
            font-family: 'Arial', sans-serif;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 30%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .content {
            text-align: center;
            margin-top: 10px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 150px;
            margin-bottom: 0px;
        }
    </style>    
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
    </div>
    <label>Foto de Perfil:</label><br>
        <img id="foto_perfil" src="fotos/default.png" width="120"><br><br>

        <input type="file" name="foto" id="foto" accept="image/*"><br><br>

        <button type="submit">Actualizar</button>

    <div class="contenedor-principal">
        <!-- Panel Izquierdo -->
        <div class="panel-izquierdo">
            <h3>Lista de Estudiantes</h3>
            <select id="lista_estudiantes" size="20">
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Panel Derecho -->
        <div class="panel-derecho">
            <h3>Datos del Estudiante</h3>
            <form id="form_estudiante">
                <input type="hidden" name="id" id="estudiante_id">

                <label>Nombre Completo:</label><br>
                <input type="text" name="nombre_completo" id="nombre_completo" required><br>

                <label>Dirección:</label><br>
                <textarea name="direccion" id="direccion"></textarea><br>

                <label>Teléfono Casa:</label><br>
                <input type="text" name="telefono_casa" id="telefono_casa"><br>

                <label>Teléfono Móvil:</label><br>
                <input type="text" name="telefono_movil" id="telefono_movil"><br>

                <label>Teléfono Emergencia:</label><br>
                <input type="text" name="telefono_emergencia" id="telefono_emergencia"><br>

                <label>Grado:</label><br>
                <input type="text" name="grado_ingreso" id="grado_ingreso" required><br>

                <label><input type="checkbox" name="activo" id="activo"> Estudiante Activo</label><br><br>

                <button type="submit">Actualizar</button>
            </form>

            <div id="mensaje_actualizacion"></div>
        </div>

        <div class="form-section" id="datos_padres_madres" style="display: none;">
            <h3>Datos del Padre</h3>
            <input type="hidden" id="padre_id">
            <input type="text" id="padre_nombre" placeholder="Nombre" required><br>
            <input type="text" id="padre_apellido" placeholder="Apellido" required><br>
            <input type="text" id="padre_celular" placeholder="Celular"><br>
            <input type="email" id="padre_email" placeholder="Email"><br>

            <h3>Datos de la Madre</h3>
            <input type="hidden" id="madre_id">
            <input type="text" id="madre_nombre" placeholder="Nombre" required><br>
            <input type="text" id="madre_apellido" placeholder="Apellido" required><br>
            <input type="text" id="madre_celular" placeholder="Celular"><br>
            <input type="email" id="madre_email" placeholder="Email"><br>

            <button type="button" id="actualizar_padres_madres">Actualizar Padres/Madres</button>
        </div>
        
        <div class="panel-derecho">
        <h3>Búsqueda de Padres/Madres</h3>
        <input type="text" id="buscar_padre_madre" placeholder="Escriba el nombre...">
        <ul id="resultados_padre_madre"></ul>

        <script src="js/buscador_padres_madres.js"></script>
    
        
        <div class="form-section" id="ficha_medica_section">
            <h3>Ficha Médica</h3>
            <form id="form_ficha_medica">
                <input type="hidden" name="estudiante_id" id="estudiante_id_medica">

                <label>Contacto Emergencia:</label><br>
                <input type="text" name="contacto_emergencia" id="contacto_emergencia"><br>

                <label>Teléfono 1:</label><br>
                <input type="text" name="telefono_emergencia1" id="telefono_emergencia1"><br>

                <label>Teléfono 2:</label><br>
                <input type="text" name="telefono_emergencia2" id="telefono_emergencia2"><br>

                <label>Observaciones:</label><br>
                <textarea name="observaciones" id="observaciones"></textarea><br>

                <label><input type="checkbox" name="dislexia" id="dislexia"> Dislexia</label><br>
                <label><input type="checkbox" name="atencion" id="atencion"> Déficit de Atención</label><br>
                <label><input type="checkbox" name="otros" id="otros"> Otros</label><br>

                <label>Información Adicional:</label><br>
                <textarea name="info_adicional" id="info_adicional"></textarea><br>

                <button type="button" id="actualizar_ficha_medica">Actualizar Ficha Médica</button>
            </form>
        </div>

        <script src="js/ficha_medica.js"></script>

    </div>

    <script src="js/planilla_ajax.js"></script>
</body>
</html>