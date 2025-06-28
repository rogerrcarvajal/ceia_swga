<?php
require_once "conn/conexion.php";

// --- Actualizar Padre ---
if (!empty($_POST['padre_id'])) {
    $stmt = $conn->prepare("UPDATE padres SET 
                                nombre = :nombre, 
                                apellido = :apellido, 
                                fecha_nacimiento = :fecha_nacimiento,
                                cedula_pasaporte = :cedula_pasaporte,
                                nacionalidad = :nacionalidad,
                                idioma = :idioma,
                                profesion = :profesion,
                                empresa = :empresa,
                                telefono_trabajo = :telefono_trabajo,
                                celular = :celular, 
                                email = :email 
                            WHERE id = :id");
    $stmt->execute([
        ':nombre' => $_POST['padre_nombre'],
        ':apellido' => $_POST['padre_apellido'],
        ':fecha_nacimiento' => $_POST['padre_fecha_nacimiento'],
        ':cedula_pasaporte' => $_POST['padre_cedula_pasaporte'],
        ':nacionalidad' => $_POST['padre_nacionalidad'],
        ':idioma' => $_POST['padre_idioma'],
        ':profesion' => $_POST['padre_profesion'],
        ':empresa' => $_POST['padre_empresa'],
        ':telefono_trabajo' => $_POST['padre_telefono_trabajo'],
        ':celular' => $_POST['padre_celular'],
        ':email' => $_POST['padre_email'],
        ':id' => $_POST['padre_id']
    ]);
}

// --- Actualizar Madre ---
if (!empty($_POST['madre_id'])) {
    $stmt = $conn->prepare("UPDATE madres SET 
                                nombre = :nombre, 
                                apellido = :apellido,
                                fecha_nacimiento = :fecha_nacimiento,
                                cedula_pasaporte = :cedula_pasaporte,
                                nacionalidad = :nacionalidad,
                                idioma = :idioma,
                                profesion = :profesion,
                                empresa = :empresa,
                                telefono_trabajo = :telefono_trabajo,
                                celular = :celular, 
                                email = :email 
                            WHERE id = :id");
    $stmt->execute([
        ':nombre' => $_POST['madre_nombre'],
        ':apellido' => $_POST['madre_apellido'],
        ':fecha_nacimiento' => $_POST['madre_fecha_nacimiento'],
        ':cedula_pasaporte' => $_POST['madre_cedula_pasaporte'],
        ':nacionalidad' => $_POST['madre_nacionalidad'],
        ':idioma' => $_POST['madre_idioma'],
        ':profesion' => $_POST['madre_profesion'],
        ':empresa' => $_POST['madre_empresa'],
        ':telefono_trabajo' => $_POST['madre_telefono_trabajo'],
        ':celular' => $_POST['madre_celular'],
        ':email' => $_POST['madre_email'],
        ':id' => $_POST['madre_id']
    ]);
}

echo "✅ Datos de padres/madres actualizados correctamente.";
?>
