<?php
require_once 'config.php';
require_once 'session.php';

/**
 * Clase para gestionar usuarios y sus permisos
 */
class UserManager {
    private $conn;
    private $currentUser;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->currentUser = [
            'id' => $_SESSION['user_id'] ?? null,
            'rol' => $_SESSION['rol'] ?? null
        ];
    }

    /**
     * Crear un nuevo usuario
     */
    public function createUser($userData) {
        // Verificar permisos según el rol que se intenta crear
        if ($userData['rol'] === 'superadmin' || 
           ($userData['rol'] === 'admin' && $this->currentUser['rol'] !== 'superadmin')) {
            throw new Exception('No tienes permisos para crear usuarios de este nivel.');
        }

        // Si es admin intentando crear un usuario normal
        if ($this->currentUser['rol'] === 'admin' && $userData['rol'] !== 'user') {
            throw new Exception('Los administradores solo pueden crear usuarios normales.');
        }

        try {
            $stmt = $this->conn->prepare('
                INSERT INTO usuarios (username, password, nombre, email, rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?)
            ');

            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            return $stmt->execute([
                $userData['username'],
                $hashedPassword,
                $userData['nombre'],
                $userData['email'],
                $userData['rol'],
                'activo'
            ]);
        } catch (PDOException $e) {
            throw new Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar contraseña de usuario
     */
    public function updatePassword($userId, $newPassword) {
        // Obtener información del usuario a modificar
        $targetUser = $this->getUserById($userId);
        
        if (!$targetUser) {
            throw new Exception('Usuario no encontrado.');
        }

        // Verificar permisos
        if (!$this->canManageUser($targetUser['rol'])) {
            throw new Exception('No tienes permisos para modificar este usuario.');
        }

        try {
            $stmt = $this->conn->prepare('
                UPDATE usuarios 
                SET password = ? 
                WHERE id = ?
            ');

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            throw new Exception('Error al actualizar la contraseña: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar estado del usuario (activar/desactivar)
     */
    public function updateUserStatus($userId, $newStatus) {
        $targetUser = $this->getUserById($userId);
        
        if (!$targetUser) {
            throw new Exception('Usuario no encontrado.');
        }

        if (!$this->canManageUser($targetUser['rol'])) {
            throw new Exception('No tienes permisos para modificar este usuario.');
        }

        try {
            $stmt = $this->conn->prepare('
                UPDATE usuarios 
                SET estado = ? 
                WHERE id = ?
            ');

            return $stmt->execute([$newStatus, $userId]);
        } catch (PDOException $e) {
            throw new Exception('Error al actualizar el estado del usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar rol de usuario (solo SuperAdmin)
     */
    public function updateUserRole($userId, $newRole) {
        if ($this->currentUser['rol'] !== 'superadmin') {
            throw new Exception('Solo el SuperAdmin puede cambiar roles de usuarios.');
        }

        try {
            $stmt = $this->conn->prepare('
                UPDATE usuarios 
                SET rol = ? 
                WHERE id = ?
            ');

            return $stmt->execute([$newRole, $userId]);
        } catch (PDOException $e) {
            throw new Exception('Error al actualizar el rol del usuario: ' . $e->getMessage());
        }
    }

    /**
     * Obtener lista de usuarios según permisos
     */
    public function getUsers() {
        $sql = 'SELECT id, username, nombre, email, rol, estado, ultimo_acceso FROM usuarios WHERE 1=1';
        
        // Si es admin, solo ve usuarios normales
        if ($this->currentUser['rol'] === 'admin') {
            $sql .= " AND rol = 'user'";
        }
        
        // Si es usuario normal, no ve nada
        if ($this->currentUser['rol'] === 'user') {
            return [];
        }

        try {
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error al obtener la lista de usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Obtener usuario por ID
     */
    private function getUserById($userId) {
        try {
            $stmt = $this->conn->prepare('
                SELECT id, username, nombre, email, rol, estado 
                FROM usuarios 
                WHERE id = ?
            ');
            
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si puede gestionar un usuario según su rol
     */
    private function canManageUser($targetUserRole) {
        // SuperAdmin puede gestionar todos los usuarios
        if ($this->currentUser['rol'] === 'superadmin') {
            return true;
        }

        // Admin solo puede gestionar usuarios normales
        if ($this->currentUser['rol'] === 'admin') {
            return $targetUserRole === 'user';
        }

        // Usuarios normales no pueden gestionar otros usuarios
        return false;
    }
}
?>
