<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\ViewRenderer;

class Controller
{
    protected ViewRenderer $view;

    public function __construct()
    {
        $this->view = new ViewRenderer();
    }

    protected function render(string $view, array $data = []): string
    {
        $menuService = new \OmniPOS\Services\MenuService();
        $role = \OmniPOS\Core\Session::get('role');
        
        // Auto-inject menus for the layout
        $globalData = [
            'sidebarMenus' => $menuService->getMenus('sidebar', $role),
            'headerMenus' => $menuService->getMenus('header', $role)
        ];

        return $this->view->render($view, array_merge($globalData, $data));
    }

    protected function checkPermission(string $permission): void
    {
        // 1. Super Admin pasa siempre
        if (in_array(\OmniPOS\Core\Session::get('role'), ['account_admin', 'super_admin'])) {
            return;
        }

        // 2. Verificar permiso granular
        if (!\OmniPOS\Services\RbacService::can($permission)) {
            header('HTTP/1.1 403 Forbidden');
            echo "<div style='font-family: sans-serif; padding: 2rem; max-width: 600px; margin: 5rem auto; border: 1px solid #fee2e2; background: #fef2f2; border-radius: 12px; color: #991b1b;'>";
            echo "<h1 style='margin-top:0;'>403 Acción No Autorizada</h1>";
            echo "<p>No tienes el permiso (<strong>{$permission}</strong>) para realizar esta operación.</p>";
            echo "<a href='/dashboard' style='display:inline-block; margin-top:1rem; padding: 0.5rem 1rem; background:#991b1b; color:white; text-decoration:none; border-radius:6px; font-weight:600;'>Volver al Dashboard</a>";
            echo "</div>";
            exit();
        }
    }
}
