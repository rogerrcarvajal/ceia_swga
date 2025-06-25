<?php
session_start();
require_once "conn/conexion.php";

$mensaje = "";

// Obtener período escolar activo
$periodo = $conn->query("SELECT id, nombre_periodo FROM periodos_escolares WHERE activo = TRUE LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("⚠️ No hay período escolar activo. Dirijase al menú Mantenimiento para crear uno.");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Insertar ESTUDIANTES
        $nombre_completo = $_POST['nombre_completo'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $lugar_nacimiento = $_POST['lugar_nacimiento'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? '';
        $idioma = $_POST['idioma'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono_casa = $_POST['telefono_casa'] ?? '';
        $telefono_movil = $_POST['telefono_movil'] ?? '';
        $telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
        $grado_ingreso = $_POST['grado_ingreso'] ?? '';
        $fecha_inscripcion = $_POST['fecha_inscripcion'] ?? '';
        $recomendado_por = $_POST['recomendado_por'] ?? '';
        $activo = isset($_POST['activo']) && $_POST['activo'] === 'on';

        $sql = "INSERT INTO estudiantes (nombre_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, grado_ingreso, fecha_inscripcion, recomendado_por, activo) 
                VALUES (:nombre_completo, :fecha_nacimiento, :lugar_nacimiento, :nacionalidad, :idioma, :direccion, :telefono_casa, :telefono_movil, :telefono_emergencia, :grado_ingreso, :fecha_inscripcion, :recomendado_por, :activo)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre_completo', $nombre_completo);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':lugar_nacimiento', $lugar_nacimiento);
        $stmt->bindParam(':nacionalidad', $nacionalidad);
        $stmt->bindParam(':idioma', $idioma);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono_casa', $telefono_casa);
        $stmt->bindParam(':telefono_movil', $telefono_movil);
        $stmt->bindParam(':telefono_emergencia', $telefono_emergencia);
        $stmt->bindParam(':grado_ingreso', $grado_ingreso);
        $stmt->bindParam(':fecha_inscripcion', $fecha_inscripcion);
        $stmt->bindParam(':recomendado_por', $recomendado_por);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
        $stmt->execute();


        // Insertar PADRE
        $nombre = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula_pasaporte = $_POST['cedula_pasaporte'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? '';
        $idiomas = $_POST['idiomas'] ?? '';
        $profesion = $_POST['profesion'] ?? '';
        $empresa = $_POST['empresa'] ?? '';
        $telefono_trabajo = $_POST['telefono_trabajo'] ?? '';
        $celular = $_POST['celular'] ?? '';
        $email = $_POST['email'] ?? '';
        

        $sql = "INSERT INTO padres (nombre, apellido, fecha_nacimiento, cedula_pasaporte, nacionalidad, idiomas, profesion, empresa, telefono_trabajo, celular, email)
        VALUES (:nombre, :apellido, :fecha_nacimiento, :cedula_pasaporte, :nacionalidad, :idioma, :profesion, :empresa, :telefono_trabajo, :celular, :email)";
        
        $stmt_padre = $conn->prepare($sql);
        $stmt_padre->bindParam(':nombre', $nombre);
        $stmt_padre->bindParam(':apellido', $apellido);
        $stmt_padre->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt_padre->bindParam(':cedula_pasaporte', $cedula_pasaporte);
        $stmt_padre->bindParam(':nacionalidad', $nacionalidad);
        $stmt_padre->bindParam(':idiomas', $idiomas);
        $stmt_padre->bindParam(':profesion', $profesion);
        $stmt_padre->bindParam(':empresa', $empresa);
        $stmt_padre->bindParam(':telefono_trabajo', $telefono_trabajo);
        $stmt_padre->bindParam(':celular', $celular);
        $stmt_padre->bindParam(':email', $email);
        $stmt_padre->execute();
        $padre_id = $conn->lastInsertId();

        // Insertar MADRE
        $nombre = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula_pasaporte = $_POST['cedula_pasaporte'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? '';
        $idiomas = $_POST['idiomas'] ?? '';
        $profesion = $_POST['profesion'] ?? '';
        $empresa = $_POST['empresa'] ?? '';
        $telefono_trabajo = $_POST['telefono_trabajo'] ?? '';
        $celular = $_POST['celular'] ?? '';
        $email = $_POST['email'] ?? '';

        $sql = "INSERT INTO madres (nombre, apellido, fecha_nacimiento, cedula_pasaporte, nacionalidad, idiomas, profesion, empresa, telefono_trabajo, celular, email) 
        VALUES (:nombre, :apellido, :fecha_nacimiento, :cedula_pasaporte, :nacionalidad, :idioma, :profesion, :empresa, :telefono_trabajo, :celular, :email)";

        $stmt_madre = $conn->prepare($sql);
        $stmt_padre->bindParam(':nombre', $nombre);
        $stmt_padre->bindParam(':apellido', $apellido);
        $stmt_padre->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt_padre->bindParam(':cedula_pasaporte', $cedula_pasaporte);
        $stmt_padre->bindParam(':nacionalidad', $nacionalidad);
        $stmt_padre->bindParam(':idiomas', $idiomas);
        $stmt_padre->bindParam(':profesion', $profesion);
        $stmt_padre->bindParam(':empresa', $empresa);
        $stmt_padre->bindParam(':telefono_trabajo', $telefono_trabajo);
        $stmt_padre->bindParam(':celular', $celular);
        $stmt_padre->bindParam(':email', $email);
        $stmt_padre->execute();
        $madre_id = $conn->lastInsertId();

        $mensaje = "✅ Estudiante registrado exitosamente.";
    } catch (PDOException $e) {
        $mensaje = "❌ Error en el registro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla de Inscripción - CEIA</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        
        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        .formulario-contenedor {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 85%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .form-seccion {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0057A0;
            padding-bottom: 5px;
        }

        .content {
            text-align: center;
            margin-top: 100px;
            color: white;
            text-shadow: 1px 1px 2px black;
        }

        .content img {
            width: 200px;
            margin-bottom: 20px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .formulario h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .alerta {
            background: #ddffdd;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid green;
        }

        .alerta-error {
            background: #ffdddd;
            padding: 10px;
            margin: 10px 0;
            border-left: 5px solid red;
        }
    </style>
</head>
<?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>PLANILLA DE INSCRIPCIÓN</h1></br>
    </div>

    <div class="formulario-contenedor">
        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>
        
        <div class="form-seccion">
            <form method="POST">
                <label>Nombre completo del estudiante:</label>
                <input type="text" name="nombre_completo" required>

                <label>Fecha de nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required>

                <label>Lugar de nacimiento:</label>
                <input type="text" name="lugar_nacimiento" required>

                <label>Nacionalidad:</label>
                <input type="text" name="nacionalidad" required>

                <label>Idioma:</label>
                <input type="text" name="idioma" required>

                <label>Dirección:</label>
                <textarea name="direccion" required></textarea>

                <label>Teléfono de casa:</label>
                <input type="text" name="telefono_casa">

                <label>Teléfono móvil:</label>
                <input type="text" name="telefono_movil">

                <label>Teléfono de emergencia:</label>
                <input type="text" name="telefono_emergencia">

                <label>Grado al que se inscribe:</label>
                <input type="text" name="grado_ingreso" required>

                <label>Fecha de Inscripción:</label>
                <input type="date" name="fecha_inscripcion" required>

                <label>Recomendado por:</label>
                <input type="text" name="recomendado_por" required>

                <label><input type="checkbox" name="activo"> Estudiante Activo</label><br><br>
            </div>

            <div class="form-seccion">
            <form method="POST">
                <label>Nombres del Padre:</label>
                <input type="text" name="nombre" required>

                <label>Apellidos del Padre:</label>
                <input type="text" name="apellido" required>

                <label>Fecha de nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required>

                <label>Cédula:</label>
                <input type="text" name="cedula_pasaporte" required>

                <label>Nacionalidad:</label>
                <input type="text" name="nacionalidad" required>

                <label>Idioma:</label>
                <input type="text" name="idiomas" required>

                <label>Profesion:</label>
                <input type="text" name="profesion" required>

                <label>Empresa:</label>
                <input type="text" name="empresa">

                <label>Teléfono de Trabajo:</label>
                <input type="text" name="telefono_trabajo">

                <label>Teléfono móvil:</label>
                <input type="text" name="celular">

                <label>Correo electrónico:</label>
                <input type="text" name="email">
            </div>

            <div class="form-seccion">
            <form method="POST">
                <label>Nombres de la madre:</label>
                <input type="text" name="nombre" required>

                <label>Apellidos de la Madre:</label>
                <input type="text" name="apellido" required>

                <label>Fecha de nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required>

                <label>Cédula:</label>
                <input type="text" name="cedula_pasaporte" required>

                <label>Nacionalidad:</label>
                <input type="text" name="nacionalidad" required>

                <label>Idioma:</label>
                <input type="text" name="idiomas" required>

                <label>Profesion:</label>
                <input type="text" name="profesion" required>

                <label>Empresa:</label>
                <input type="text" name="empresa">

                <label>Teléfono de Trabajo:</label>
                <input type="text" name="telefono_trabajo">

                <label>Teléfono móvil:</label>
                <input type="text" name="celular">

                <label>Correo electrónico:</label>
                <input type="text" name="email">
            </div>

            <button type="submit">Guardar Inscripción</button>
        </form>
    </div>
</body>
</html>