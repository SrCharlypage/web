<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// Si el usuario ya está autenticado, redirigir al index
if (isAuthenticated()) {
    header('Location: index.html');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        try {
            // Debug: Guardar información en un archivo de log
            error_log("Intento de inicio de sesión - Usuario: " . $username);
            
            $stmt = $conn->prepare('SELECT id, username, password, nombre, rol, estado FROM usuarios WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug: Registrar si se encontró el usuario
            if ($user) {
                error_log("Usuario encontrado en la base de datos");
                error_log("Hash almacenado: " . $user['password']);
                error_log("Verificación de contraseña: " . (password_verify($password, $user['password']) ? "EXITOSA" : "FALLIDA"));
            } else {
                error_log("Usuario no encontrado en la base de datos");
            }

            if ($user && password_verify($password, $user['password'])) {
                if ($user['estado'] === 'inactivo') {
                    $error = 'Esta cuenta está desactivada. Contacte con el administrador.';
                } else {
                    // Iniciar sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol'] = $user['rol'];
                    
                    // Actualizar último acceso
                    $stmt = $conn->prepare('UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?');
                    $stmt->execute([$user['id']]);

                    header('Location: index.html');
                    exit();
                }
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = 'Error al intentar iniciar sesión.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestión de Hermandad</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
