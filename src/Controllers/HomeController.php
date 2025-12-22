<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Services\ConfigService;
use OmniPOS\Services\MenuService;

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $config = ConfigService::getInstance();
        $menuService = new MenuService();

        $siteName = $config->get('site_name', 'OmniPOS SaaS');
        $menus = $menuService->getMenus('header', 'public');

        return $this->render('home', [
            'siteName' => $siteName,
            'menus' => $menus,
            'title' => 'Inicio'
        ]);
    }
}
