<?php
// Verificar que las constantes no estén definidas antes de definirlas
if (!defined('DB_HOST')) {
    // Configuración de la base de datos
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'hermandad_db');

    // Configuración general
    define('SITE_NAME', 'Gestión de Hermandad');
    define('BASE_URL', 'http://localhost/Hdad_Claude');
}

// Conexión a la base de datos
try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
