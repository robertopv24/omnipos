<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Session;
use OmniPOS\Core\Database;
use PDO;

class RbacService
{
    protected static array $userPermissions = [];

    public static function loadPermissions(): void
    {
        if (!empty(self::$userPermissions))
            return;

        $role = Session::get('role');
        if (!$role)
            return;

        $pdo = Database::connect();
        $sql = "SELECT p.name FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN roles r ON rp.role_id = r.id 
                WHERE r.name = :role";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['role' => $role]);
        self::$userPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function hasRole(string $role): bool
    {
        // Simple hierarchy: account_admin > admin > manager > user
        $userRole = Session::get('role');

        if (!$userRole)
            return false;
        if (in_array($userRole, ['account_admin', 'super_admin']))
            return true; // Super admin
        if ($userRole === $role)
            return true;

        return false;
    }

    public static function can(string $permission): bool
    {
        $userRole = Session::get('role');
        if (in_array($userRole, ['account_admin', 'super_admin'])) {
            return true;
        }

        self::loadPermissions();
        return in_array($permission, self::$userPermissions);
    }

    public static function hasPermission(string $permission): bool
    {
        return self::can($permission);
    }

    /**
     * Valida la contraseña de un usuario con rol de supervisor (admin/manager)
     * para autorizar operaciones sensibles sin cerrar la sesión actual.
     * Retorna el ID del supervisor si es válido, null si no.
     */
    public static function validateSupervisor(string $password): ?string
    {
        $pdo = Database::connect();
        $businessId = Session::get('business_id');

        // Buscar usuarios admin o manager en este negocio
        $sql = "SELECT id, password FROM users 
                WHERE business_id = :bid 
                AND role IN ('admin', 'account_admin', 'super_admin', 'manager')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        $supervisors = $stmt->fetchAll();

        foreach ($supervisors as $s) {
            if (password_verify($password, $s['password'])) {
                return $s['id'];
            }
        }

        return null;
    }
}
