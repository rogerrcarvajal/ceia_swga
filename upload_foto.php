<?php
require_once "conn/conexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $foto = $_FILES['foto'];

    if ($foto['error'] == 0) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $ruta = "fotos/" . "foto_" . $id . "." . $ext;

        move_uploaded_file($foto['tmp_name'], $ruta);

        $stmt = $conn->prepare("UPDATE estudiantes SET foto_perfil = :foto WHERE id = :id");
        $stmt->execute([':foto' => $ruta, ':id' => $id]);

        echo "✅ Foto actualizada correctamente.";
    } else {
        echo "❌ Error al subir la foto.";
    }
}
?>