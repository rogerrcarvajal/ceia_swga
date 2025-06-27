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

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

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

        // Insertar PADRES
            $sql = "INSERT INTO padres (padre_nombre, padre_apellido, padre_fecha_nacimiento, padre_cedula_pasaporte, padre_nacionalidad, padre_idioma, padre_profesion, padre_empresa, padre_telefono_trabajo, padre_celular, padre_email)
            VALUES (:padre_nombre, :padre_apellido, :padre_fecha_nacimiento, :padre_cedula_pasaporte, :padre_nacionalidad, :padre_idioma, :padre_profesion, :padre_empresa, :padre_telefono_trabajo, :padre_celular, :padre_email)";
            
            $stmt_padres = $conn->prepare($sql);
            $stmt_padres->bindParam(':padre_nombre', $padre_nombre);
            $stmt_padres->bindParam(':padre_apellido', $padre_apellido);
            $stmt_padres->bindParam(':padre_fecha_nacimiento', $padre_fecha_nacimiento);
            $stmt_padres->bindParam(':padre_cedula_pasaporte', $padre_cedula_pasaporte);
            $stmt_padres->bindParam(':padre_nacionalidad', $padre_nacionalidad);
            $stmt_padres->bindParam(':padre_idioma', $padre_idiomas);
            $stmt_padres->bindParam(':padre_profesion', $padre_profesion);
            $stmt_padres->bindParam(':padre_empresa', $padre_empresa);
            $stmt_padres->bindParam(':padre_telefono_trabajo', $padre_telefono_trabajo);
            $stmt_padres->bindParam(':padre_celular', $padre_celular);
            $stmt_padres->bindParam(':padre_email', $padre_email);
            $stmt_padres->execute();
            $padre_id = $conn->lastInsertId();
        
        // Insertar MADRES
            $sql = "INSERT INTO madres (madre_nombre, madre_apellido, madre_fecha_nacimiento, madre_cedula_pasaporte, madre_nacionalidad, madre_idioma, madre_profesion, madre_empresa, madre_telefono_trabajo, madre_celular, madre_email) 
            VALUES (:madre_nombre, :madre_apellido, :madre_fecha_nacimiento, :madre_cedula_pasaporte, :madre_nacionalidad, :madre_idioma, :madre_profesion, :madre_empresa, :madre_telefono_trabajo, :madre_celular, :madre_email)";

            $stmt_madres = $conn->prepare($sql);
            $stmt_madres->bindParam(':madre_nombre', $madre_nombre);
            $stmt_madres->bindParam(':madre_apellido', $madre_apellido);
            $stmt_madres->bindParam(':madre_fecha_nacimiento', $madre_fecha_nacimiento);
            $stmt_madres->bindParam(':madre_cedula_pasaporte', $madre_cedula_pasaporte);
            $stmt_madres->bindParam(':madre_nacionalidad', $madre_nacionalidad);
            $stmt_madres->bindParam(':madre_idioma', $madre_idiomas);
            $stmt_madres->bindParam(':madre_profesion', $madre_profesion);
            $stmt_madres->bindParam(':madre_empresa', $madre_empresa);
            $stmt_madres->bindParam(':madre_telefono_trabajo', $madre_telefono_trabajo);
            $stmt_madres->bindParam(':madre_celular', $madre_celular);
            $stmt_madres->bindParam(':madre_email', $madre_email);
            $stmt_madres->execute();
            $madre_id = $conn->lastInsertId();

        // Insertar FICHA MÉDICA
            $stmt_ficha = $conn->prepare("INSERT INTO salud_estudiantil (estudiante_id, edad, completado_por, fecha, contacto_emergencia, relacion_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional, problemas_oido_vista, fecha_examen_oido_vista, autorizo_medicamentos, medicamentos_actuales, autorizo_emergencia)
                        VALUES (:estudiante_id, :edad, :completado_por, :fecha, :contacto_emergencia, :relacion_emergencia, :telefono1, :telefono2, :observaciones, :dislexia, :atencion, :otros, :info_adicional, :problemas_oido_vista, :fecha_examen_oido_vista, :autorizo_medicamentos, :medicamentos_actuales, :autorizo_emergencia)");
            $stmt_ficha->bindParam(':estudiante_id', $estudiante_id);
            $stmt_ficha->bindParam(':edad', $_POST['edad_estudiante']);
            $stmt_ficha->bindParam(':completado_por', $_POST['completado_por']);
            $stmt_ficha->bindParam(':fecha', $_POST['fecha_salud']);
            $stmt_ficha->bindParam(':contacto_emergencia', $_POST['contacto_emergencia']);
            $stmt_ficha->bindParam(':relacion_emergencia', $_POST['relacion_emergencia']);
            $stmt_ficha->bindParam(':telefono1', $_POST['telefono1']);
            $stmt_ficha->bindParam(':telefono2', $_POST['telefono1']);
            $stmt_ficha->bindParam(':observaciones', $_POST['observaciones']);
            $dislexia = isset($_POST['dislexia']) ? 1 : 0;
            $atencion = isset($_POST['atencion']) ? 1 : 0;
            $otros = isset($_POST['otros']) ? 1 : 0;
            $stmt_ficha->bindParam(':info_adicional', $_POST['info_adicional']);
            $stmt_ficha->bindParam(':problemas_oido_vista', $_POST['problemas_oido_vista']);
            $stmt_ficha->bindParam(':fecha_examen_oido_vista', $_POST['fecha_examen']);
            $autorizo_medicamentos = isset($_POST['autorizo_medicamentos']) ? 1 : 0;
            $stmt_ficha->bindParam(':medicamentos_actuales', $_POST['medicamentos_actuales']);
            $autorizo_emergencia = isset($_POST['autorizo_emergencia']) ? 1 : 0;
            $stmt_ficha->bindParam(':dislexia', $dislexia, PDO::PARAM_BOOL);
            $stmt_ficha->bindParam(':atencion', $atencion, PDO::PARAM_BOOL);
            $stmt_ficha->bindParam(':otros', $otros, PDO::PARAM_BOOL);
            $stmt_ficha->bindParam(':autorizo_medicamentos', $autorizo_medicamentos, PDO::PARAM_BOOL);
            $stmt_ficha->bindParam(':autorizo_emergencia', $autorizo_emergencia, PDO::PARAM_BOOL);
            $stmt_ficha->execute();

        $conn->commit();
        $mensaje = "✅ Registro completado correctamente.";
    } catch (Exception $e) {
        $conn->rollBack();
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

        .formulario {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            margin: 30px auto;
            width: 30%;
            border-radius: 8px;
        }

        .form-seccion {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
        }

        .form-seccionFM {
            width: 30%;
            color: white;
            min-width: 300px;
            margin-bottom: 20px;
            justify-content: left;
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
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>Planilla de Inscripción</h1></br>
    </div>

    <div class="formulario-contenedor">
        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>"><?= $mensaje ?></p>
        <?php endif; ?>

            <!-- Estudiante -->
            <div class="form-seccion">
                <h3>Datos del Estudiante</h3>
                <form method="POST">
                <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
                <input type="date" name="fecha_nacimiento" required>
                <input type="text" name="lugar_nacimiento" placeholder="Lugar de nacimiento" required>
                <input type="text" name="nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="idioma" placeholder="Idiomas que habla" required>
                <textarea name="direccion" placeholder="Dirección" required></textarea>
                <input type="text" name="telefono_casa" placeholder="Teléfono de casa" required>
                <input type="text" name="telefono_movil" placeholder="Teléfono celular" required>
                <input type="text" name="telefono_emergencia" placeholder="Teléfono de emergencia" required>
                <select name="grado_ingreso" required>
                    <option value="">Grado de ingreso</option>
                    <option value="Daycare">Daycare</option>
                    <option value="Pk-3">Pk-3</option>
                    <option value="Pk-4">Pk-4</option>
                    <option value="Kindergarten">Kindergarten</option>
                    <option value="Grade 1">Grade 1</option>
                    <option value="Grade 2">Grade 2</option>
                    <option value="Grade 3">Grade 3</option>
                    <option value="Grade 4">Grade 4</option>
                    <option value="Grade 5">Grade 5</option>
                    <option value="Grade 6">Grade 6</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                    <option value="Grade 11">Grade 11</option>
                    <option value="Grade 12">Grade 12</option>
                </select>
                <input type="date" name="fecha_inscripcion" required>
                <input type="text" name="recomendado_por" placeholder="Recomendado por">
                <input type="number" name="edad_estudiante" placeholder="Edad" required>

                <label><input type="checkbox" name="activo"> Estudiante Activo</label><br><br>
            </div>

            <!-- Padre -->
            <div class="form-seccion">
                <h3>Datos del Padre</h3>
                <form method="POST">
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
            </div>

            <!-- Madre -->
            <div class="form-seccion">
                <h3>Datos de la Madre</h3>
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
                <input type="email" name="madre_email" placeholder="Correo electrónico" required>
            </div>

            <!-- Ficha Médica -->
             <div class="form-seccionFM">
                <h3>Ficha Médica</h3>
                <form method="POST">
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

                <label><input type="checkbox" name="autorizo_emergencia"> Autorizo atención de emergencia</label>

                <br><br>

                <button type="submit">Guardar Inscripción</button>

            </div>
        </form>
    </div>
</body>
</html>