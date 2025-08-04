<?php
require_once '../config.php';
require_once '../session.php';
require_once '../dashboard_data.php';
require_once '../auth.php'; // Asegúrate de que este archivo define hasRole()

// Verificar que la petición sea AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    exit('Acceso no permitido');
}

// Crear instancia de DashboardData
$dashboard = new DashboardData($conn);

// Preparar respuesta
$response = [];

// Datos según el rol del usuario
if (hasRole('admin')) {
    $response['total_hermanos'] = $dashboard->getTotalHermanosActivos();
    $response['altas_anuales'] = $dashboard->getAltasAnuales();
    $response['cuotas_pendientes'] = $dashboard->getCuotasPendientes();
    $response['total_recaudado'] = $dashboard->getTotalRecaudado();
    
    if (isSuperAdmin()) {
        $response['usuarios_activos'] = $dashboard->getUsuariosActivos();
        $ultimos_accesos = $dashboard->getUltimosAccesos();
        $response['ultimos_accesos'] = '';
        foreach ($ultimos_accesos as $acceso) {
            $response['ultimos_accesos'] .= sprintf(
                "<div>%s - %s</div>",
                htmlspecialchars($acceso['username']),
                date('d/m/Y H:i', strtotime($acceso['ultimo_acceso']))
            );
        }
    }
} else {
    // Obtener el ID del hermano asociado al usuario actual
    $hermanoId = $_SESSION['hermano_id'] ?? null;
    if ($hermanoId) {
        $response['estado_cuotas'] = $dashboard->getEstadoCuotasHermano($hermanoId);
        $response['estado_participacion'] = $dashboard->getEstadoParticipacion($hermanoId);
    }
}

// Enviar respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
