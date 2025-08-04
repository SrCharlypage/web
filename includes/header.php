<?php
// header.php
// Sesión iniciada en páginas que incluyen header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables de usuario
$username = $_SESSION['nombre'] ?? 'Usuario';
$role     = $_SESSION['rol']     ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Sistema de Gestión - Hermandad'); ?></title>
    <!-- Font Awesome -->
    <link href="/cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    /* Estilos generales */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: #333; }
    .main-header { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-bottom: 3px solid #8b5a3c; position: sticky; top: 0; z-index: 1000; }
    .header-content { max-width: 1400px; margin: 0 auto; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
    .logo-section { display: flex; align-items: center; gap: 1rem; }
    .logo-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #e9eaee, #cdcadf); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .logo-icon .logo-img { max-width: 100px; height: auto; }
    .header-text h1 { color: #8b5a3c; font-size: 1.5rem; font-weight: 700; }
    .header-text p  { color: #666;   font-size: 0.9rem; font-style: italic; }
    .user-section { display: flex; align-items: center; gap: 1rem; background: rgba(139,90,60,0.1); padding: 0.5rem 1rem; border-radius: 25px; border: 2px solid rgba(139,90,60,0.2); }
    .user-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, #8b5a3c, #d4af37); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
    .user-info h3 { color: #8b5a3c; font-size: 0.9rem; font-weight: 600; }
    .user-info p  { color: #666;   font-size: 0.8rem; }
    .logout-btn { background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 20px; cursor: pointer; font-size: 0.8rem; transition: all 0.3s ease; }
    .logout-btn:hover { background: #c82333; transform: translateY(-1px); }
    .main-content { max-width: 1200px; margin: 30px auto; padding: 0 20px; text-align: center; }
    </style>
</head>
<body>
<header class="main-header">
    <div class="header-content">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="../img/logo.png" alt="Hdad. Humildad y Paciencia" class="logo-img">
            </div>
            <div class="header-text">
                <h1>Hermandad Humildad y Paciencia - San Fernando (Cádiz)</h1>
                <p>Sistema de Gestión Integral</p>
            </div>
        </div>
        <div class="user-section">
            <div class="user-avatar"><?php echo strtoupper(substr($username,0,2)); ?></div>
            <div class="user-info">
                <h3><?php echo htmlspecialchars($username); ?></h3>
                <p><?php echo htmlspecialchars($role); ?></p>
            </div>
            <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Salir</button>
        </div>
    </div>
</header>
<main class="main-content">
