<?php

namespace OmniPOS\Middleware;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Services\RbacService;

class RbacMiddleware
{
    protected ?string $requiredPermission = null;

    /**
     * Constructor opcional para pasar permisos si el router lo soporta insteractivamente,
     * pero nuestro router actual pasa params en el array de middleware.
     * Adaptaremos el handle para leer los argumentos adicionales si el Router lo permite,
     * o asumiremos que se usa una subclase/configuración específica.
     * 
     * ACTUALIZACIÓN: Nuestro Router básico no pasa argumentos al constructor del Middleware.
     * Sin embargo, para este sistema, vamos a asumir que el endpoint define el permiso.
     * 
     * ESTRATEGIA: Dado que el `Router.php` actual es simple, vamos a hacer una mejora:
     * El `RbacMiddleware` verificará los permisos basándose en la ruta o usaremos
     * una convención de nombres, O MEJOR: El Router debería permitir pasar parámetros.
     * 
     * Por ahora, para esta fase, haremos que compruebe si es 'account_admin' o 'admin' para 
     * rutas de administración, y dejaremos la granularidad fina para una mejora del Router.
     * 
     * PERO el usuario pidió corregirlo. La forma más rápida sin reescribir todo el Router es:
     * Verificar permisos hardcodeados en el middleware basándose en el Path, O
     * usar `RbacService::hasRole('admin')` como fallback seguro.
     */

    public function handle(Request $request, Response $response): bool
    {
        // 1. Super Admin siempre pasa sin preguntas
        if (in_array(Session::get('role'), ['account_admin', 'super_admin'])) {
            return true;
        }

        // 2. Verificar si el usuario está logueado
        if (!Session::get('user_id')) {
            $response->redirect('/login');
            return false;
        }

        // 3. Lógica de permisos basada en rutas
        $path = $request->getPath();
        
        // Rutas que requieren permisos específicos
        $permissionMap = [
            '/users' => 'manage_users',
            '/account/settings' => 'manage_settings',
            '/products' => 'manage_products',
            '/inventory' => 'manage_inventory',
            '/purchases' => 'manage_purchases',
            '/suppliers' => 'manage_purchases',
            '/finance' => 'view_finance',
            '/cash' => 'manage_cash',
            '/manufacture' => 'manage_manufacture',
            '/restoration' => 'view_restoration',
            '/reports' => 'view_reports',
            '/admin' => 'manage_platform',
            '/platform' => 'manage_platform',
        ];

        foreach ($permissionMap as $routePrefix => $permission) {
            if (strpos($path, $routePrefix) === 0) {
                if (!RbacService::can($permission)) {
                    // Si no tiene el permiso específico, ver si es Admin al menos (fallback legacy)
                    if (Session::get('role') === 'admin') {
                        return true; 
                    }
                    
                    // Denegar acceso con detalle informativo para debug si es necesario
                    $this->forbiddenResponse($permission);
                    return false;
                }
            }
        }

        return true;
    }

    private function forbiddenResponse(string $permission): void
    {
        header('HTTP/1.1 403 Forbidden');
        echo "<div style='font-family: sans-serif; padding: 2rem; max-width: 600px; margin: 5rem auto; border: 1px solid #fee2e2; background: #fef2f2; border-radius: 12px; color: #991b1b;'>";
        echo "<h1 style='margin-top:0;'>403 Acceso Denegado</h1>";
        echo "<p>No tienes el permiso requerido (<strong>{$permission}</strong>) para acceder a esta sección.</p>";
        echo "<hr style='border: 0; border-top: 1px solid #fecaca; margin: 1.5rem 0;'>";
        echo "<p style='font-size: 0.85rem; color: #b91c1c;'>Si crees que esto es un error, contacta al administrador de la plataforma.</p>";
        echo "<a href='/dashboard' style='display:inline-block; margin-top:1rem; padding: 0.5rem 1rem; background:#991b1b; color:white; text-decoration:none; border-radius:6px; font-weight:600;'>Volver al Dashboard</a>";
        echo "</div>";
    }
}
