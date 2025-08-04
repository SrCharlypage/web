<?php
// Asegurarse de que la sesión no se haya iniciado
if (session_status() === PHP_SESSION_NONE) {
    // Configuración de la sesión
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Lax');

    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // Iniciar la sesión
    session_start();
}
