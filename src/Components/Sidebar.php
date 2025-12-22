<?php

namespace OmniPOS\Components;

use OmniPOS\Services\MenuService;
use OmniPOS\Core\Session;

class Sidebar
{
    protected MenuService $menuService;

    public function __construct()
    {
        $this->menuService = new MenuService();
    }

    public function render(): string
    {
        $role = Session::get('role', 'public');
        $sidebarMenus = $this->menuService->getMenus('sidebar', $role);
        
        // Retornar la vista parcial del sidebar
        // Como no tenemos un motor de componentes complejo, usamos el sistema de vistas básico
        return $this->renderSidebar($sidebarMenus);
    }

    private function renderSidebar(array $menus): string
    {
        ob_start();
        include __DIR__ . '/../Views/components/sidebar.php';
        return ob_get_clean();
    }
}
