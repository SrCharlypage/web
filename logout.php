<?php
require_once 'includes/session.php';

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: ' . BASE_URL . '/login.html');
exit();
?>
