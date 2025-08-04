<?php
// Incluir la configuración de la sesión antes que nada
require_once __DIR__ . '/session_config.php';

// Incluir la configuración general
require_once __DIR__ . '/config.php';

// Incluir el sistema de permisos
require_once __DIR__ . '/permissions.php';

// Verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Verificar el rol del usuario
function getUserRole() {
    return $_SESSION['rol'] ?? null;
}

// Verificar si el usuario es superadmin (Se mantiene aquí por compatibilidad)
function isSuperAdmin() {
    return getUserRole() === 'superadmin';
}

// Verificar si el usuario es admin (Se mantiene aquí por compatibilidad)
function isAdmin() {
    return getUserRole() === 'admin' || isSuperAdmin();
}

// Redirigir si no está autenticado
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}
?>
