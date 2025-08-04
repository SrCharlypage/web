<?php
/**
 * Funciones auxiliares para obtener datos del dashboard
 */
class DashboardData {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtener total de hermanos activos
     */
    public function getTotalHermanosActivos() {
        try {
            $stmt = $this->conn->query("
                SELECT COUNT(*) as total 
                FROM hermanos 
                WHERE estado = 'activo'
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error obteniendo total hermanos: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener altas del año actual
     */
    public function getAltasAnuales() {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM historico_estado_hermano 
                WHERE tipo_movimiento = 'ALTA' 
                AND YEAR(fecha_movimiento) = ?
            ");
            $stmt->execute([date('Y')]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error obteniendo altas anuales: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener total de cuotas pendientes
     */
    public function getCuotasPendientes() {
        try {
            $stmt = $this->conn->query("
                SELECT COUNT(DISTINCT h.id) as total
                FROM hermanos h
                INNER JOIN cuotas_hermano ch ON h.id = ch.hermano_id
                LEFT JOIN pagos p ON ch.id = p.cuota_hermano_id
                WHERE h.estado = 'activo'
                AND (p.estado = 'pendiente' OR p.estado IS NULL)
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error obteniendo cuotas pendientes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener total recaudado en el año actual
     */
    public function getTotalRecaudado() {
        try {
            $stmt = $this->conn->prepare("
                SELECT COALESCE(SUM(p.importe), 0) as total
                FROM pagos p
                WHERE p.estado = 'pagado'
                AND YEAR(p.fecha_pago) = ?
            ");
            $stmt->execute([date('Y')]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return number_format($result['total'], 2);
        } catch (PDOException $e) {
            error_log("Error obteniendo total recaudado: " . $e->getMessage());
            return '0.00';
        }
    }

    /**
     * Obtener número de usuarios activos hoy
     */
    public function getUsuariosActivos() {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total
                FROM usuarios
                WHERE DATE(ultimo_acceso) = CURDATE()
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error obteniendo usuarios activos: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener últimos accesos al sistema
     */
    public function getUltimosAccesos($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT username, ultimo_acceso
                FROM usuarios
                WHERE ultimo_acceso IS NOT NULL
                ORDER BY ultimo_acceso DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo últimos accesos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estado de cuotas de un hermano específico
     */
    public function getEstadoCuotasHermano($hermanoId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_cuotas,
                    SUM(CASE WHEN p.estado = 'pagado' THEN 1 ELSE 0 END) as cuotas_pagadas
                FROM cuotas_hermano ch
                LEFT JOIN pagos p ON ch.id = p.cuota_hermano_id
                WHERE ch.hermano_id = ?
                AND ch.fecha_fin IS NULL
            ");
            $stmt->execute([$hermanoId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total_cuotas'] == 0) {
                return 'Sin cuotas asignadas';
            }
            
            if ($result['total_cuotas'] == $result['cuotas_pagadas']) {
                return 'Al corriente';
            }
            
            $pendientes = $result['total_cuotas'] - $result['cuotas_pagadas'];
            return "Pendientes: {$pendientes} cuota(s)";
        } catch (PDOException $e) {
            error_log("Error obteniendo estado cuotas: " . $e->getMessage());
            return 'Error al verificar';
        }
    }

    /**
     * Obtener estado de participación en procesión
     */
    public function getEstadoParticipacion($hermanoId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    pa.tipo_participacion,
                    pa.estado_pago,
                    COALESCE(s.descripcion, a.nombre) as ubicacion
                FROM participacion_anual pa
                LEFT JOIN secciones_cortejo s ON pa.seccion_id = s.id
                LEFT JOIN atributos_cortejo a ON pa.atributo_id = a.id
                WHERE pa.hermano_id = ?
                AND pa.año = ?
            ");
            $stmt->execute([$hermanoId, date('Y')]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return 'No inscrito';
            }
            
            return sprintf(
                "%s - %s (%s)",
                $result['tipo_participacion'],
                $result['ubicacion'],
                $result['estado_pago']
            );
        } catch (PDOException $e) {
            error_log("Error obteniendo estado participación: " . $e->getMessage());
            return 'Error al verificar';
        }
    }
}
?>
