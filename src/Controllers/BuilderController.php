<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Database;
use OmniPOS\Services\OrphanViewService;

class BuilderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Check Admin
        // if (!RbacService::isSuperAdmin()) ... handled by middleware
    }

    // --- Visual Builder ---

    public function index(Request $request, Response $response)
    {
        $id = $request->getParam('id');
        return $this->render('platform/builder/index', [
            'title' => 'Constructor Visual Pro',
            'pageId' => $id
        ]);
    }

    public function pages(Request $request, Response $response)
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT id, title, slug, access_level, updated_at FROM dynamic_pages ORDER BY updated_at DESC");
        $pages = $stmt->fetchAll();
        $response->json(['success' => true, 'pages' => $pages]);
    }

    public function load(Request $request, Response $response)
    {
        $id = $request->getParam('id');
        $slug = $request->getParam('slug');

        $pdo = Database::connect();
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM dynamic_pages WHERE id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM dynamic_pages WHERE slug = ?");
            $stmt->execute([$slug]);
        }
        
        $page = $stmt->fetch();
        if ($page) {
            $page['layout'] = json_decode($page['layout_json'], true);
            $response->json(['success' => true, 'page' => $page]);
        } else {
            $response->json(['success' => false, 'error' => 'Página no encontrada']);
        }
    }

    public function save(Request $request, Response $response)
    {
        $data = $request->getBody();
        
        $pdo = Database::connect();
        
        // Use ID if provided for update, else use slug
        $id = $data['id'] ?? null;
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE dynamic_pages SET title = ?, layout_json = ?, slug = ?, access_level = ? WHERE id = ?");
            $stmt->execute([$data['title'], json_encode($data['layout']), $data['slug'], $data['access_level'] ?? 'auth', $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO dynamic_pages (id, slug, title, layout_json, access_level) VALUES (UUID(), ?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE layout_json = VALUES(layout_json), title = VALUES(title), access_level = VALUES(access_level)");
            $stmt->execute([$data['slug'], $data['title'], json_encode($data['layout']), $data['access_level'] ?? 'auth']);
        }
        
        $response->json(['success' => true, 'message' => 'Página guardada']);
    }

    // --- Orphan Importer ---

    public function importer(Request $request, Response $response)
    {
        return $this->render('platform/importer/index', [
            'title' => 'Importador de Vistas Huérfanas'
        ]);
    }

    public function scanOrphans(Request $request, Response $response)
    {
        $service = new OrphanViewService();
        $orphans = $service->scan();
        $response->json(['orphans' => $orphans]);
    }

    public function bindView(Request $request, Response $response)
    {
        $data = $request->getBody();
        $service = new OrphanViewService();
        
        try {
            $service->bindView(
                $data['slug'], 
                $data['viewPath'], 
                $data['title'], 
                $data['menuGroup'] ?? null
            );
            $response->json(['success' => true]);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- Dynamic Components ---

    public function components(Request $request, Response $response)
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM builder_components ORDER BY category, name ASC");
        $components = $stmt->fetchAll();
        $response->json(['success' => true, 'components' => $components]);
    }

    public function saveComponent(Request $request, Response $response)
    {
        $data = $request->getBody();
        $pdo = Database::connect();
        
        $id = $data['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("UPDATE builder_components SET name = ?, icon = ?, category = ?, tag_name = ?, html_content = ?, is_container = ? WHERE id = ?");
            $stmt->execute([
                $data['name'], $data['icon'], $data['category'], 
                $data['tag_name'] ?? 'div', $data['html_content'], 
                $data['is_container'] ?? 0, $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO builder_components (id, name, icon, category, tag_name, html_content, is_container) VALUES (UUID(), ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'], $data['icon'], $data['category'], 
                $data['tag_name'] ?? 'div', $data['html_content'], 
                $data['is_container'] ?? 0
            ]);
        }
        
        $response->json(['success' => true, 'message' => 'Componente guardado']);
    }

    public function deleteComponent(Request $request, Response $response)
    {
        $id = $request->getParam('id');
        if(!$id) return $response->json(['success' => false, 'error' => 'ID requerido']);
        
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM builder_components WHERE id = ?");
        $stmt->execute([$id]);
        
        $response->json(['success' => true, 'message' => 'Componente eliminado']);
    }
}
