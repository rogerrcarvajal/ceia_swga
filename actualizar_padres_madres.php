<?php
require_once "conn/conexion.php";

// --- Actualizar Padre ---
if (!empty($_POST['padre_id'])) {
    $stmt = $conn->prepare("UPDATE padres SET 
                                padre_nombre = :padre_nombre, 
                                padre_apellido = :padre_apellido, 
                                padre_fecha_nacimiento = :padre_fecha_nacimiento,
                                padre_cedula_pasaporte = :padre_cedula_pasaporte,
                                padre_nacionalidad = :padre_nacionalidad,
                                padre_idioma = :padre_idioma,
                                padre_profesion = :padre_profesion,
                                padre_empresa = :padre_empresa,
                                padre_telefono_trabajo = :padre_telefono_trabajo,
                                padre_celular = :padre_celular, 
                                padre_email = :padre_email 
                            WHERE id = :id");
    $stmt->execute([
        ':padre_nombre' => $_POST['padre_nombre'],
        ':padre_apellido' => $_POST['padre_apellido'],
        ':padre_fecha_nacimiento' => $_POST['padre_fecha_nacimiento'],
        ':padre_cedula_pasaporte' => $_POST['padre_cedula_pasaporte'],
        ':padre_nacionalidad' => $_POST['padre_nacionalidad'],
        ':padre_idioma' => $_POST['padre_idioma'],
        ':padre_profesion' => $_POST['padre_profesion'],
        ':padre_empresa' => $_POST['padre_empresa'],
        ':padre_telefono_trabajo' => $_POST['padre_telefono_trabajo'],
        ':padre_celular' => $_POST['padre_celular'],
        ':padre_email' => $_POST['padre_email'],
        ':id' => $_POST['padre_id']
    ]);
}

// --- Actualizar Madre ---
if (!empty($_POST['madre_id'])) {
    $stmt = $conn->prepare("UPDATE madres SET 
                                madre_nombre = :madre_nombre, 
                                madre_apellido = :madre_apellido,
                                madre_fecha_nacimiento = :madre_fecha_nacimiento,
                                madre_cedula_pasaporte = :madre_cedula_pasaporte,
                                madre_nacionalidad = :madre_nacionalidad,
                                madre_idioma = :madre_idioma,
                                madre_profesion = :madre_profesion,
                                madre_empresa = :madre_empresa,
                                madre_telefono_trabajo = :madre_telefono_trabajo,
                                madre_celular = :madre_celular, 
                                madre_email = :madre_email 
                            WHERE id = :id");
    $stmt->execute([
        ':madre_nombre' => $_POST['madre_nombre'],
        ':madre_apellido' => $_POST['madre_apellido'],
        ':madre_fecha_nacimiento' => $_POST['madre_fecha_nacimiento'],
        ':madre_cedula_pasaporte' => $_POST['madre_cedula_pasaporte'],
        ':madre_nacionalidad' => $_POST['madre_nacionalidad'],
        ':madre_idioma' => $_POST['madre_idioma'],
        ':madre_profesion' => $_POST['madre_profesion'],
        ':madre_empresa' => $_POST['madre_empresa'],
        ':madre_telefono_trabajo' => $_POST['madre_telefono_trabajo'],
        ':madre_celular' => $_POST['madre_celular'],
        ':madre_email' => $_POST['madre_email'],
        ':id' => $_POST['madre_id']
    ]);
}

echo "✅ Datos de padres/madres actualizados correctamente.";
?>
