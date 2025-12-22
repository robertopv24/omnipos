<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;

class OrphanViewService
{
    private string $viewPath;

    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../Views/';
    }

    public function scan(): array
    {
        $allFiles = $this->getDirContents($this->viewPath);
        $orphans = [];
        
        $pdo = Database::connect();
        
        // Fetch all mapped routes
        $mappings = $pdo->query("SELECT target_path FROM route_mappings")->fetchAll(\PDO::FETCH_COLUMN);
        
        // Fetch all menus (rough check, though menus link to URLs not files directly)
        // We rely mainly on route_mappings for "registered" status in the new system.
        
        foreach ($allFiles as $file) {
            // Get relative path without extension
            $relativePath = str_replace([$this->viewPath, '.php'], '', $file);
            
            // Skip partials, layouts, and components
            if (strpos($relativePath, 'layouts/') === 0 || strpos($relativePath, 'components/') === 0) {
                continue;
            }

            // Check if mapped
            if (!in_array($relativePath, $mappings)) {
                $orphans[] = [
                    'path' => $relativePath,
                    'file' => $file
                ];
            }
        }

        return $orphans;
    }

    public function bindView(string $slug, string $viewPath, string $title, string $menuGroup = null)
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        try {
            // 1. Create Route Mapping
            $stmt = $pdo->prepare("INSERT INTO route_mappings (id, slug, target_path, access_level) VALUES (UUID(), ?, ?, 'auth')");
            $stmt->execute([$slug, $viewPath]);

            // 2. Add to Menu (if requested)
            if ($menuGroup) {
                // Find parent menu
                $parent = $pdo->query("SELECT id FROM menus WHERE title = '$menuGroup' AND type='sidebar' LIMIT 1")->fetch();
                $parentId = $parent ? $parent['id'] : null;

                $stmt = $pdo->prepare("INSERT INTO menus (id, parent_id, title, url, type, visibility) VALUES (UUID(), ?, ?, ?, 'sidebar', 'private')");
                $url = '/' . ltrim($slug, '/');
                $stmt->execute([$parentId, $title, $url]);
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private function getDirContents($dir, &$results = [])
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                    $results[] = $path;
                }
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
            }
        }
        return $results;
    }
}
