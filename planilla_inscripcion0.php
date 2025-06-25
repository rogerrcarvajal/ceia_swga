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

        // Registrar padre
        $sql_padre = "INSERT INTO padres (nombre, apellido, fecha_nacimiento, cedula_pasaporte, nacionalidad, idiomas, profesion, empresa, telefono_trabajo, celular, email)
                      VALUES (:nombre, :apellido, :fecha_nacimiento, :cedula, :nacionalidad, :idiomas, :profesion, :empresa, :telefono_trabajo, :celular, :email)";
        $stmt_padre = $conn->prepare($sql_padre);
        $stmt_padre->execute([
            ':nombre' => $_POST['padre_nombre'],
            ':apellido' => $_POST['padre_apellido'],
            ':fecha_nacimiento' => $_POST['padre_fecha_nacimiento'],
            ':cedula' => $_POST['padre_cedula'],
            ':nacionalidad' => $_POST['padre_nacionalidad'],
            ':idiomas' => $_POST['padre_idiomas'],
            ':profesion' => $_POST['padre_profesion'],
            ':empresa' => $_POST['padre_empresa'],
            ':telefono_trabajo' => $_POST['padre_telefono_trabajo'],
            ':celular' => $_POST['padre_celular'],
            ':email' => $_POST['padre_email']
        ]);
        $padre_id = $conn->lastInsertId();

        // Registrar madre
        $sql_madre = "INSERT INTO madres (nombre, apellido, fecha_nacimiento, cedula_pasaporte, nacionalidad, idiomas, profesion, empresa, telefono_trabajo, celular, email)
                      VALUES (:nombre, :apellido, :fecha_nacimiento, :cedula, :nacionalidad, :idiomas, :profesion, :empresa, :telefono_trabajo, :celular, :email)";
        $stmt_madre = $conn->prepare($sql_madre);
        $stmt_madre->execute([
            ':nombre' => $_POST['madre_nombre'],
            ':apellido' => $_POST['madre_apellido'],
            ':fecha_nacimiento' => $_POST['madre_fecha_nacimiento'],
            ':cedula' => $_POST['madre_cedula'],
            ':nacionalidad' => $_POST['madre_nacionalidad'],
            ':idiomas' => $_POST['madre_idiomas'],
            ':profesion' => $_POST['madre_profesion'],
            ':empresa' => $_POST['madre_empresa'],
            ':telefono_trabajo' => $_POST['madre_telefono_trabajo'],
            ':celular' => $_POST['madre_celular'],
            ':email' => $_POST['madre_email']
        ]);
        $madre_id = $conn->lastInsertId();

        $activo = isset($_POST['activo']) && $_POST['activo'] === 'on';

        // Registrar estudiante
        $sql_est = "INSERT INTO estudiantes (nombre_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, grado_ingreso, fecha_inscripcion, recomendado_por, padre_id, madre_id, activo, periodo_id)
                    VALUES (:nombre, :fecha_nac, :lugar_nac, :nacionalidad, :idioma, :direccion, :tel_casa, :tel_movil, :tel_emergencia, :grado, :fecha_insc, :recomendado, :padre_id, :madre_id, :activo, :periodo_id)";
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
        $stmt_est = $conn->prepare($sql_est);
        $stmt_est->execute([
            ':nombre' => $_POST['nombre_estudiante'],
            ':fecha_nac' => $_POST['fecha_nacimiento_estudiante'],
            ':lugar_nac' => $_POST['lugar_nacimiento_estudiante'],
            ':nacionalidad' => $_POST['nacionalidad_estudiante'],
            ':idioma' => $_POST['idioma_estudiante'],
            ':direccion' => $_POST['direccion_estudiante'],
            ':tel_casa' => $_POST['tel_casa_estudiante'] ?: null,
            ':tel_movil' => $_POST['tel_movil_estudiante'] ?: null,
            ':tel_emergencia' => $_POST['tel_emergencia_estudiante'] ?: null,
            ':grado' => $_POST['grado_estudiante'],
            ':fecha_insc' => $_POST['fecha_inscripcion_estudiante'],
            ':recomendado' => $_POST['recomendado_estudiante'],
            ':padre_id' => $padre_id,
            ':madre_id' => $madre_id,
            ':activo' => $activo,
            ':periodo_id' => $periodo['id']
        ]);
        $estudiante_id = $conn->lastInsertId();

        // Registrar ficha médica
        $sql_salud = "INSERT INTO salud_estudiantil (estudiante_id, edad, completado_por, fecha, contacto_emergencia, relacion_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional, problemas_oido_vista, fecha_examen_oido_vista, autorizo_medicamentos, medicamentos_actuales, autorizo_emergencia)
                      VALUES (:estudiante_id, :edad, :completado_por, :fecha, :contacto_emergencia, :relacion_emergencia, :telefono1, :telefono2, :observaciones, :dislexia, :atencion, :otros, :info_adicional, :problemas_oido_vista, :fecha_examen_oido_vista, :autorizo_medicamentos, :medicamentos_actuales, :autorizo_emergencia)";
        $stmt_salud = $conn->prepare($sql_salud);
        $stmt_salud->execute([
            ':estudiante_id' => $estudiante_id,
            ':edad' => $_POST['edad_estudiante'],
            ':completado_por' => $_POST['completado_por'],
            ':fecha' => $_POST['fecha_salud'],
            ':contacto_emergencia' => $_POST['contacto_emergencia'],
            ':relacion_emergencia' => $_POST['relacion_emergencia'],
            ':telefono1' => $_POST['telefono_emergencia1'],
            ':telefono2' => $_POST['telefono_emergencia2'],
            ':observaciones' => $_POST['observaciones'],
            ':dislexia' => isset($_POST['dislexia']),
            ':atencion' => isset($_POST['atencion']),
            ':otros' => isset($_POST['otros']),
            ':info_adicional' => $_POST['info_adicional'],
            ':problemas_oido_vista' => $_POST['problemas_oido_vista'],
            ':fecha_examen_oido_vista' => $_POST['fecha_examen'],
            ':autorizo_medicamentos' => isset($_POST['autorizo_medicamentos']),
            ':medicamentos_actuales' => $_POST['medicamentos_actuales'],
            ':autorizo_emergencia' => isset($_POST['autorizo_emergencia'])
        ]);

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
    <title>Planilla de Inscripción</title>
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

        input, textarea, select {
            width: 95%;
            padding: 8px;
            margin: 8px 0;
        }

        button {
            padding: 12px 25px;
            background-color: #0057A0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #003d73;
        }

        .mensaje {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: green;
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

        .dashboard {
            background-color: red;
            padding: 5px 10px;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <img src="img/logo_ceia.png" alt="Logo CEIA">
        <h1><br>PLANILLA DE INSCRIPCIÓN</h1></br>
    </div>
    <div class="formulario-contenedor">
        <?php if ($mensaje): ?>
            <p class="<?= strpos($mensaje, '✅') !== false ? 'alerta' : 'alerta-error' ?>"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST" style="width: 100%; display: flex; flex-wrap: wrap; justify-content: space-around;">

            <!-- Estudiante -->
            <div class="form-seccion">
                <h3>Datos del Estudiante</h3>
                <input type="text" name="nombre_estudiante" placeholder="Nombre completo" required>
                <input type="date" name="fecha_nacimiento_estudiante" required>
                <input type="text" name="lugar_nacimiento_estudiante" placeholder="Lugar de nacimiento" required>
                <input type="text" name="nacionalidad_estudiante" placeholder="Nacionalidad" required>
                <input type="text" name="idioma_estudiante" placeholder="Idiomas que habla" required>
                <textarea name="direccion_estudiante" placeholder="Dirección" required></textarea>
                <input type="text" name="tel_casa_estudiante" placeholder="Teléfono de casa" required>
                <input type="text" name="tel_movil_estudiante" placeholder="Teléfono celular" required>
                <input type="text" name="tel_emergencia_estudiante" placeholder="Teléfono de emergencia" required>
                <input type="text" name="grado_estudiante" placeholder="Grado de ingreso" required>
                <input type="date" name="fecha_inscripcion_estudiante" required>
                <input type="text" name="recomendado_estudiante" placeholder="Recomendado por">
                <input type="number" name="edad_estudiante" placeholder="Edad" required>
            </div>

            <!-- Padre -->
            <div class="form-seccion">
                <h3>Datos del Padre</h3>
                <input type="text" name="padre_nombre" placeholder="Nombre" required>
                <input type="text" name="padre_apellido" placeholder="Apellido" required>
                <input type="date" name="padre_fecha_nacimiento" required>
                <input type="text" name="padre_cedula" placeholder="Cédula o Pasaporte" required>
                <input type="text" name="padre_nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="padre_idiomas" placeholder="Idiomas que habla" required>
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
                <input type="text" name="madre_cedula" placeholder="Cédula o Pasaporte" required>
                <input type="text" name="madre_nacionalidad" placeholder="Nacionalidad" required>
                <input type="text" name="madre_idiomas" placeholder="Idiomas que habla" required>
                <input type="text" name="madre_profesion" placeholder="Profesión" required>
                <input type="text" name="madre_empresa" placeholder="Empresa donde trabaja" required>
                <input type="text" name="madre_telefono_trabajo" placeholder="Teléfono trabajo" required>
                <input type="text" name="madre_celular" placeholder="Celular" required>
                <input type="email" name="madre_email" placeholder="Correo electrónico" required>
            </div>

            <!-- Ficha Médica -->
            <div class="form-seccion" style="width: 30%;">
                <h3>Ficha Médica</h3>
                <input type="text" name="completado_por" placeholder="Completado por" required>
                <input type="date" name="fecha_salud" required>
                <input type="text" name="contacto_emergencia" placeholder="Contacto de Emergencia" required>
                <input type="text" name="relacion_emergencia" placeholder="Relación de Emergencia" required>
                <input type="text" name="telefono_emergencia1" placeholder="Teléfono 1" required>
                <input type="text" name="telefono_emergencia2" placeholder="Teléfono 2">
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
           
                <button type="submit">Registrar</button>
                               
            </div>
        </form>
    <div>
        <br>
        <a href="dashboard.php" class="boton-link">Volver al Inicio</a>
    </div>
    </div>
</body>
</html>