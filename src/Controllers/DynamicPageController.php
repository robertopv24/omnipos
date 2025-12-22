<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Database;
use OmniPOS\Services\PageRendererService;

class DynamicPageController extends Controller
{
    protected PageRendererService $renderer;

    public function __construct()
    {
        parent::__construct();
        $this->renderer = new PageRendererService();
    }

    /**
     * Handles wildcard dynamic routes
     */
    public function handleRequest(Request $request, Response $response, string $slug)
    {
        $pdo = Database::connect();
        $slug = trim($slug, '/'); // Normalize: "products/create" instead of "/products/create"
        
        // 1. Check Dynamic Pages (Builder)
        $stmt = $pdo->prepare("SELECT * FROM dynamic_pages WHERE slug = ? OR slug = ?");
        $stmt->execute([$slug, "/".$slug]);
        $page = $stmt->fetch();

        if ($page) {
            return $this->renderDynamicPage($page);
        }

        // 2. Check Route Mappings (Orphan Views)
        $stmt = $pdo->prepare("SELECT * FROM route_mappings WHERE slug = ? OR slug = ?");
        $stmt->execute([$slug, "/".$slug]);
        $mapping = $stmt->fetch();

        if ($mapping) {
            return $this->renderMappedView($mapping);
        }

        // 404
        header("HTTP/1.0 404 Not Found");
        echo "404 - Estás perdido en el espacio.";
    }

    private function renderDynamicPage(array $page)
    {
        // Security Check
        if (!$this->checkAccess($page['access_level'])) {
            return $this->showForbidden();
        }

        $layoutData = json_decode($page['layout_json'], true);
        
        // Use the renderer service to compile JSON to HTML
        $htmlContent = $this->renderer->render($layoutData);

        // Call parent render method correctly
        return parent::render('components/dynamic_wrapper', [
            'title' => $page['title'],
            'dynamicContent' => $htmlContent
        ]);
    }

    private function renderMappedView(array $mapping)
    {
        // Security Check
        if (!$this->checkAccess($mapping['access_level'] ?? 'auth')) {
            return $this->showForbidden();
        }

        // Simply render the mapped view file
        $viewPath = str_replace('.php', '', $mapping['target_path']);
        
        return parent::render($viewPath, [
            'title' => $mapping['title'] ?? 'Vista Dinámica'
        ]);
    }

    private function checkAccess(?string $level): bool
    {
        if (!$level || $level === 'public') return true;
        
        // Auth Check
        if (!\OmniPOS\Core\Session::has('user_id')) {
            return false;
        }

        if ($level === 'auth') return true;

        // Role Checks
        $userRole = \OmniPOS\Core\Session::get('role');
        
        if ($level === 'super_admin') return $userRole === 'super_admin';
        if ($level === 'admin') return in_array($userRole, ['admin', 'super_admin']);

        return false;
    }

    private function showForbidden()
    {
        header('HTTP/1.1 403 Forbidden');
        return $this->render('errors/403', [
            'message' => "Acceso no autorizado para este nivel."
        ]);
    }
}
