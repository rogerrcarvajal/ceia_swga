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
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear y activar uno.");
}

$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Expedientes</title>
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
        <h1><br>Administrar Expedientes</h1></br>
    </div>
    <label>Foto de Perfil:</label><br>
        <img id="foto_perfil" src="fotos/default.png" width="120"><br><br>

        <input type="file" name="foto" id="foto" accept="image/*"><br><br>

        <button type="submit">Actualizar</button>

        <div class="contenedor-principal">
        <!-- Panel Izquierdo -->
        <div class="panel-izquierdo" size= "20">
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
                <input type="text" name="nombre_completo" placeholder="Nombres completo" required>
                <input type="text" name="apellido_completo" placeholder="Apellidos completo" required>
                <input type="date" name="fecha_nacimiento" required>
                <input type="text" name="lugar_nacimiento" placeholder="Lugar de nacimiento" required>
                <input type="text" name="nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="idioma" placeholder="Idiomas que habla" required>
                <textarea name="direccion" placeholder="Dirección" required></textarea>
                <input type="text" name="telefono_casa" placeholder="Teléfono de casa" required>
                <input type="text" name="telefono_movil" placeholder="Teléfono celular" required>
                <input type="text" name="telefono_emergencia" placeholder="Teléfono de emergencia" required>
                <input type="text" name="grado_ingreso" placeholder="Grado de ingreso" required>
                <input type="date" name="fecha_inscripcion" required>
                <input type="text" name="recomendado_por" placeholder="Recomendado por">
                <input type="number" name="edad_estudiante" placeholder="Edad" required>

                <label><input type="checkbox" name="activo"> Estudiante Activo</label><br><br>

                <button type="submit">Actualizar</button>
            </form>
        </div>

        <div id="mensaje_actualizacion"></div>

        <div class="panel-derecho">
            <h3>Datos del Padre</h3>
            <form id="datos_padres_madres">                
                <input type="hidden" id="padre_id">
                <input type="text" name="padre_nombre" placeholder="Nombre" required>
                <input type="text" name="padre_apellido" placeholder="Apellido" required>
                <input type="date" name="padre_fecha_nacimiento" required>
                <input type="text" name="padre_cedula_pasaporte" placeholder="Cédula o Pasaporte" required>
                <input type="text" name="padre_nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="padre_idioma" placeholder="Idiomas que habla" required>
                <input type="text" name="padre_profesion" placeholder="Profesión" required>
                <input type="text" name="padre_empresa" placeholder="Empresa donde trabaja" required>
                <input type="text" name="padre_telefono_trabajo" placeholder="Teléfono trabajo" required>
                <input type="text" name="padre_celular" placeholder="Celular" required>
                <input type="email" name="padre_email" placeholder="Correo electrónico" required>

                <h3>Datos de la Madre</h3>
                <input type="hidden" id="madre_id">
                <input type="text" name="madre_nombre" placeholder="Nombre" required>
                <input type="text" name="madre_apellido" placeholder="Apellido" required>
                <input type="date" name="madre_fecha_nacimiento" required>
                <input type="text" name="madre_cedula_pasaporte" placeholder="Cédula o Pasaporte" required>
                <input type="text" name="madre_nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="madre_idioma" placeholder="Idiomas que habla" required>
                <input type="text" name="madre_profesion" placeholder="Profesión" required>
                <input type="text" name="madre_empresa" placeholder="Empresa donde trabaja" required>
                <input type="text" name="madre_telefono_trabajo" placeholder="Teléfono trabajo" required>
                <input type="text" name="madre_celular" placeholder="Celular" required>
                <input type="email" name="madre_email" placeholder="Correo electrónico" required><br><br>

                <button type="button" id="actualizar_padres_madres">Actualizar Padres/Madres</button>
            </form>
        </div>
        
        <div class="panel-derecho">
            <h3>Ficha Médica</h3>
            <form id="ficha_medica_section">             
                <input type="hidden" name="estudiante_id" id="estudiante_id_medica">
                <input type="text" name="completado_por" placeholder="Completado por" required>
                <input type="date" name="fecha_salud" required>
                <input type="text" name="contacto_emergencia" placeholder="Contacto de Emergencia" required>
                <input type="text" name="relacion_emergencia" placeholder="Relación de Emergencia" required>
                <input type="text" name="Teléfono1" placeholder="Teléfono 1" required>
                <input type="text" name="Teléfono1" placeholder="Teléfono 2">
                <textarea name="observaciones" placeholder="Observaciones"></textarea>
                <label><input type="checkbox" name="dislexia"> Dislexia</label>
                <label><input type="checkbox" name="atencion"> Déficit de Atención</label>
                <label><input type="checkbox" name="otros"> Otros</label>
                <textarea name="info_adicional" placeholder="Información adicional"></textarea>
                <textarea name="problemas_oido_vista" placeholder="Problemas de oído/vista"></textarea>
                <input type="text" name="fecha_examen" placeholder="Fecha último examen oído/vista">
                <label><input type="checkbox" name="autorizo_medicamentos"> Autorizo administración de medicamentos</label>
                <textarea name="medicamentos_actuales" placeholder="Medicamentos actuales"></textarea>
                <label><input type="checkbox" name="autorizo_emergencia"> Autorizo atención de emergencia</label><br><br>

                <button type="button" id="actualizar_ficha_medica">Actualizar Ficha Médica</button>
            </form>    
        </div>

        <script src="js/ficha_medica.js"></script>

    </div>

    <script src="js/planilla_ajax.js"></script>
</body>
</html>