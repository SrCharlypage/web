<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

// Verificar permisos
checkPermission('gestion_hermanos');

// Verificar que sea una petición AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso no permitido');
}

// Obtener parámetros
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$orderBy = isset($_GET['orderBy']) ? trim($_GET['orderBy']) : 'numero_hermano';

// Validar orden
$validOrders = ['numero_hermano', 'apellidos', 'fecha_alta'];
if (!in_array($orderBy, $validOrders)) {
    $orderBy = 'numero_hermano';
}

// Calcular offset
$offset = ($page - 1) * $perPage;

try {
    // Construir consulta base
    $sql = "SELECT h.*, 
            (SELECT fecha_pago 
             FROM pagos p 
             JOIN cuotas_hermano ch ON p.cuota_hermano_id = ch.id 
             WHERE ch.hermano_id = h.id 
             ORDER BY fecha_pago DESC 
             LIMIT 1) as ultima_cuota
            FROM hermanos h
            WHERE 1=1";
    
    $params = [];
    
    // Añadir condiciones de búsqueda
    if (!empty($search)) {
        $sql .= " AND (h.nombre LIKE ? OR h.apellidos LIKE ? OR h.dni LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
    }
    
    if (!empty($estado)) {
        $sql .= " AND h.estado = ?";
        $params[] = $estado;
    }
    
    // Contar total de registros para paginación
    $countSql = "SELECT COUNT(*) as total FROM ($sql) as count_table";
    $stmt = $conn->prepare($countSql);
    $stmt->execute($params);
    $totalRows = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Añadir orden y límites
    $sql .= " ORDER BY $orderBy ASC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    // Ejecutar consulta principal
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $hermanos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'hermanos' => $hermanos,
        'totalPages' => ceil($totalRows / $perPage),
        'currentPage' => $page,
        'totalRows' => $totalRows
    ];
    
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Error al obtener los datos: ' . $e->getMessage()
    ];
}

// Enviar respuesta
header('Content-Type: application/json');
echo json_encode($response);
?>
