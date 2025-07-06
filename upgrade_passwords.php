<?php
// --- SCRIPT DE UN SOLO USO PARA ACTUALIZAR CONTRASEÑAS A FORMATO HASHEADO ---
// Coloca este archivo en la raíz de tu proyecto (ceia_swga_organizado/)
// ¡¡BORRAR DESPUÉS DE USAR!!

require_once __DIR__ . '/src/config.php';

echo "<h1>Actualizador de Contraseñas</h1>";

try {
    // 1. Obtener todos los usuarios.
    $stmt = $conn->query("SELECT id, username, password FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $actualizados = 0;
    $ya_hasheados = 0;

    if (empty($usuarios)) {
        echo "<p>No se encontraron usuarios en la base de datos.</p>";
        exit;
    }

    echo "<ul>";

    // 2. Recorrer cada usuario.
    foreach ($usuarios as $usuario) {
        $id = $usuario['id'];
        $username = $usuario['username'];
        $password_plano = $usuario['password'];

        // 3. Verificar si la contraseña ya parece estar hasheada.
        // Los hashes de BCRYPT (el default) siempre tienen 60 caracteres.
        if (strlen($password_plano) === 60 && strpos($password_plano, '$2y$') === 0) {
            echo "<li>Usuario '{$username}' ya tiene una contraseña segura. No se requiere acción.</li>";
            $ya_hasheados++;
            continue; // Saltar al siguiente usuario
        }

        // 4. Si no está hasheada, proceder a encriptarla.
        echo "<li>Procesando usuario '{$username}'... ";
        $hashed_password = password_hash($password_plano, PASSWORD_DEFAULT);

        // 5. Actualizar la base de datos con la nueva contraseña hasheada.
        $update_sql = "UPDATE usuarios SET password = :password WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([
            ':password' => $hashed_password,
            ':id' => $id
        ]);
        
        echo "<strong>¡Contraseña actualizada con éxito!</strong></li>";
        $actualizados++;
    }

    echo "</ul>";
    echo "<h2>Proceso completado.</h2>";
    echo "<p style='color:green;'><strong>Usuarios actualizados:</strong> {$actualizados}</p>";
    echo "<p style='color:blue;'><strong>Usuarios que ya estaban seguros:</strong> {$ya_hasheados}</p>";
    echo "<p style='color:red; font-weight:bold;'>ACCIÓN REQUERIDA: ¡Por seguridad, borra este archivo (upgrade_passwords.php) de tu servidor ahora!</p>";

} catch (PDOException $e) {
    die("<h2>ERROR DE BASE DE DATOS:</h2><p>" . $e->getMessage() . "</p>");
}
?>