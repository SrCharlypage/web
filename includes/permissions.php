<?php
/**
 * Definición de permisos y acceso por roles
 */

// Definición de permisos por rol
$ROLE_PERMISSIONS = [
    'superadmin' => [
        'dashboard' => true,
        'gestion_hermanos' => true,
        'gestion_usuarios' => true,
        'gestion_economia' => true,
        'gestion_prendas' => true,
        'gestion_cortejo' => true,
        'listados' => true,
        'configuracion' => true
    ],
    'admin' => [
        'dashboard' => true,
        'gestion_hermanos' => true,
        'gestion_usuarios' => false,
        'gestion_economia' => true,
        'gestion_prendas' => true,
        'gestion_cortejo' => true,
        'listados' => true,
        'configuracion' => false
    ],
    'user' => [
        'dashboard' => true,
        'gestion_hermanos' => true,
        'gestion_usuarios' => false,
        'gestion_economia' => false,
        'gestion_prendas' => false,
        'gestion_cortejo' => false,
        'listados' => true,
        'configuracion' => false
    ]
];

// Nombres amigables de los módulos y sus URLs
$MODULE_NAMES = [
    'dashboard' => 'Panel de Control',
    'gestion_hermanos' => 'Gestión de Hermanos',
    'gestion_usuarios' => 'Gestión de Usuarios',
    'gestion_economia' => 'Gestión Económica',
    'gestion_prendas' => 'Gestión de Prendas',
    'gestion_cortejo' => 'Gestión del Cortejo',
    'listados' => 'Listados',
    'configuracion' => 'Configuración'
];

// URLs de los módulos
$MODULE_URLS = [
    'dashboard' => '',
    'gestion_hermanos' => 'admin/hermanos',
    'gestion_usuarios' => 'admin/usuarios.php',
    'gestion_economia' => 'admin/economia',
    'gestion_prendas' => 'admin/prendas',
    'gestion_cortejo' => 'admin/cortejo',
    'listados' => 'admin/informes',
    'configuracion' => 'admin/configuracion'
];

/**
 * Verifica si el usuario actual tiene permiso para un módulo específico
 */
function hasPermission($module) {
    global $ROLE_PERMISSIONS;
    
    if (!isset($_SESSION['rol'])) {
        return false;
    }
    
    $role = $_SESSION['rol'];
    return isset($ROLE_PERMISSIONS[$role][$module]) && $ROLE_PERMISSIONS[$role][$module];
}

/**
 * Verifica el acceso y redirige si no tiene permisos
 */
function checkPermission($module) {
    if (!hasPermission($module)) {
        header('Location: ' . BASE_URL . '/access_denied.php');
        exit();
    }
}

/**
 * Obtiene los módulos disponibles para el rol del usuario actual
 */
function getAvailableModules() {
    global $ROLE_PERMISSIONS, $MODULE_NAMES;
    
    if (!isset($_SESSION['rol'])) {
        return [];
    }
    
    $role = $_SESSION['rol'];
    $modules = [];
    
    foreach ($ROLE_PERMISSIONS[$role] as $module => $hasAccess) {
        if ($hasAccess) {
            $modules[$module] = $MODULE_NAMES[$module];
        }
    }
    
    return $modules;
}
?>
