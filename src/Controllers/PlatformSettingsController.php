<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Database;
use OmniPOS\Core\Session;

class PlatformSettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->view->setLayout('admin');
        
        // Safety check for role
        // Allow account_admin too if they have permission (handled by Middleware)
        // But for consistency with PlatformAccountController:
        if (!in_array(Session::get('role'), ['account_admin', 'super_admin'])) {
            // Only admins can access platform settings
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Gestión de Planes de Suscripción
     */
    public function plans(Request $request, Response $response)
    {
        return $this->render('platform/plans', [
            'title' => 'Gestión de Planes de Suscripción',
            'plans' => [
                ['id' => 'basic', 'name' => 'Básico', 'price' => 29.99, 'features' => 'POS, Inventario, 1 Sucursal'],
                ['id' => 'pro', 'name' => 'Profesional', 'price' => 59.99, 'features' => 'POS, Inventario, Restauración, 3 Sucursales'],
                ['id' => 'enterprise', 'name' => 'Enterprise', 'price' => 149.99, 'features' => 'Todo Ilimitado, Soporte Prioritario']
            ]
        ]);
    }

    /**
     * Gestión de Idiomas / Traducciones
     */
    public function languages(Request $request, Response $response)
    {
        $i18nPath = __DIR__ . '/../I18n';
        $files = glob($i18nPath . '/*.json');
        
        $baseLangFile = $i18nPath . '/es.json';
        $baseKeys = file_exists($baseLangFile) ? count(json_decode(file_get_contents($baseLangFile), true)) : 1;

        $languages = [];
        foreach ($files as $file) {
            $iso = basename($file, '.json');
            $translations = json_decode(file_get_contents($file), true) ?: [];
            $count = count($translations);
            $progress = round(($count / $baseKeys) * 100);
            
            $languages[] = [
                'iso' => $iso,
                'name' => $this->getLangName($iso),
                'count' => $count,
                'progress' => $progress > 100 ? 100 : $progress
            ];
        }

        return $this->render('platform/languages', [
            'title' => 'Gestión de Idiomas y Traducciones',
            'languages' => $languages
        ]);
    }

    private function getLangName($iso) {
        $names = ['es' => 'Español (Latam)', 'en' => 'English (US)', 'pt' => 'Português', 'fr' => 'Français', 'it' => 'Italiano', 'de' => 'Deutsch'];
        return $names[$iso] ?? strtoupper($iso);
    }

    public function getTranslations(Request $request, Response $response)
    {
        $iso = $request->getParam('iso');
        $path = __DIR__ . "/../I18n/$iso.json";
        if (!file_exists($path)) return $response->json(['success' => false, 'error' => 'Archivo no encontrado']);
        
        $data = json_decode(file_get_contents($path), true);
        return $response->json(['success' => true, 'translations' => $data]);
    }

    public function saveLanguage(Request $request, Response $response)
    {
        $data = $request->getBody();
        $iso = strtolower(preg_replace('/[^a-z]/', '', $data['iso'] ?? ''));
        if (!$iso) return $response->json(['success' => false, 'error' => 'ISO requerido']);
        
        $path = __DIR__ . "/../I18n/$iso.json";
        if (!file_exists($path)) {
            file_put_contents($path, json_encode([], JSON_PRETTY_PRINT));
        }
        
        Session::flash('success', 'Idioma agregado: ' . $iso);
        header('Location: /platform/languages');
        exit;
    }

    public function deleteLanguage(Request $request, Response $response)
    {
        $iso = $request->getParam('iso');
        if ($iso === 'es' || $iso === 'en') return $response->json(['success' => false, 'error' => 'No se pueden eliminar idiomas base']);
        
        $path = __DIR__ . "/../I18n/$iso.json";
        if (file_exists($path)) unlink($path);
        
        return $response->json(['success' => true]);
    }

    public function updateTranslationKey(Request $request, Response $response)
    {
        $data = $request->getBody();
        $iso = $data['iso'];
        $key = $data['key'];
        $value = $data['value'];
        
        $path = __DIR__ . "/../I18n/$iso.json";
        if (!file_exists($path)) return $response->json(['success' => false, 'error' => 'Idioma no encontrado']);
        
        $translations = json_decode(file_get_contents($path), true) ?: [];
        $translations[$key] = $value;
        
        file_put_contents($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->json(['success' => true]);
    }

    /**
     * Gestión de Publicidad y Avisos
     */
    public function advertisements(Request $request, Response $response)
    {
        return $this->render('platform/advertisements', [
            'title' => 'Gestión de Publicidad y Mensajes Globales',
            'ads' => [
                ['id' => 1, 'title' => 'Nueva versión 2.0 disponible', 'type' => 'announcement', 'status' => 'active'],
                ['id' => 2, 'title' => 'Mantenimiento programado 24/12', 'type' => 'warning', 'status' => 'scheduled']
            ]
        ]);
    }

    /**
     * Configuración Global del Sistema
     */
    public function settings(Request $request, Response $response)
    {
        return $this->render('platform/settings', [
            'title' => 'Configuración Global de la Plataforma',
            'settings' => [
                'system_name' => 'OmniPOS SaaS',
                'maintenance_mode' => 'OFF',
                'default_currency_global' => 'USD',
                'support_email' => 'support@omnipos.test'
            ]
        ]);
    }

    /**
     * Gestión de Menús del Sistema
     */
    public function menus(Request $request, Response $response)
    {
        $pdo = Database::connect();
        $menus = $pdo->query("SELECT m.*, p.name as permission_name 
                             FROM menus m 
                             LEFT JOIN permissions p ON m.required_permission_id = p.id
                             ORDER BY type, position ASC")->fetchAll();
        
        // Get all root menus for parent selection
        $parents = $pdo->query("SELECT id, title, type FROM menus WHERE parent_id IS NULL ORDER BY type, title ASC")->fetchAll();
        
        // Get permissions for permission selection
        $permissions = $pdo->query("SELECT id, name FROM permissions ORDER BY name ASC")->fetchAll();

        return $this->render('platform/menus', [
            'title' => 'Editor de Menús del Sistema',
            'menus' => $menus,
            'parents' => $parents,
            'permissions' => $permissions
        ]);
    }

    public function saveMenu(Request $request, Response $response)
    {
        $data = $request->getBody();
        $pdo = Database::connect();
        
        $title = $data['title'] ?? null;
        $url = $data['url'] ?? null;
        $id = $data['id'] ?? null;

        if (!$title || !$url) {
            Session::flash('error', 'El título y la URL son obligatorios');
            header('Location: /platform/menus');
            exit;
        }

        $params = [
            $title,
            $url,
            $data['icon'] ?? 'fa fa-circle',
            (isset($data['parent_id']) && $data['parent_id'] !== '') ? $data['parent_id'] : null,
            (int)($data['position'] ?? 0),
            $data['type'] ?? 'sidebar',
            $data['visibility'] ?? 'public',
            (isset($data['required_permission_id']) && $data['required_permission_id'] !== '') ? $data['required_permission_id'] : null,
            isset($data['is_active']) ? 1 : 0
        ];

        if ($id) {
            $sql = "UPDATE menus SET title = ?, url = ?, icon = ?, parent_id = ?, position = ?, type = ?, visibility = ?, required_permission_id = ?, is_active = ? WHERE id = ?";
            $params[] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            $sql = "INSERT INTO menus (id, title, url, icon, parent_id, position, type, visibility, required_permission_id, is_active) VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

        Session::flash('success', 'Menú guardado correctamente');
        header('Location: /platform/menus');
        exit;
    }

    public function deleteMenu(Request $request, Response $response)
    {
        $id = $request->getParam('id');
        if (!$id) return $response->json(['success' => false, 'error' => 'ID requerido']);
        
        $pdo = Database::connect();
        // Also set children to null or delete them? Usually safer to just delete if it's a menu management
        $pdo->prepare("DELETE FROM menus WHERE id = ?")->execute([$id]);
        
        return $response->json(['success' => true]);
    }

    public function reorderMenus(Request $request, Response $response)
    {
        $data = $request->getBody();
        $orders = $data['orders'] ?? [];
        $pdo = Database::connect();
        
        foreach ($orders as $item) {
            $stmt = $pdo->prepare("UPDATE menus SET position = ? WHERE id = ?");
            $stmt->execute([$item['position'], $item['id']]);
        }
        
        return $response->json(['success' => true]);
    }
}
