<?php
// Funciones de utilidad para la aplicación

// Función para limpiar y validar entrada de datos
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para verificar si el usuario está autenticado
function is_authenticated() {
    return isset($_SESSION['user_id']);
}

// Función para redirigir si el usuario no está autenticado
function require_authentication() {
    if (!is_authenticated()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

// Función para determinar el módulo actual basado en la URL
function getCurrentModule() {
    global $MODULE_URLS;
    
    $currentPath = $_SERVER['REQUEST_URI'];
    $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    $relativePath = str_replace($basePath, '', $currentPath);
    
    // Eliminar parámetros GET si existen
    if (($pos = strpos($relativePath, '?')) !== false) {
        $relativePath = substr($relativePath, 0, $pos);
    }
    
    // Eliminar barra inicial si existe
    $relativePath = ltrim($relativePath, '/');
    
    // Buscar el módulo que corresponde a la URL actual
    foreach ($MODULE_URLS as $module => $url) {
        if (strpos($relativePath, $url) === 0) {
            return $module;
        }
    }
    
    // Si no se encuentra, asumir que estamos en el dashboard
    return 'dashboard';
}

// Función para generar un token CSRF
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar el token CSRF
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('Error de validación CSRF');
    }
    return true;
}

// Función para formatear fechas
function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

// Función para formatear moneda
function format_currency($amount) {
    return number_format($amount, 2, ',', '.') . ' €';
}

// Función para generar número de hermano
function generate_hermano_number() {
    global $conn;
    $stmt = $conn->query("SELECT MAX(numero_hermano) as max_num FROM hermanos");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['max_num'] ? $result['max_num'] + 1 : 1;
}

// Función para validar DNI español
function validate_dni($dni) {
    $letra = substr($dni, -1);
    $numeros = substr($dni, 0, -1);
    if (substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1) == $letra && strlen($letra) == 1 && strlen($numeros) == 8) {
        return true;
    }
    return false;
}
?>
