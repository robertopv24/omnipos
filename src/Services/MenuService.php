<?php

namespace OmniPOS\Services;

use OmniPOS\Core\Database;
use PDO;

class MenuService
{
    public function getMenus(string $type = 'sidebar', ?string $role = 'public'): array
    {
        $pdo = Database::connect();
        $accountId = \OmniPOS\Core\Session::get('account_id');

        $sql = "SELECT m.*, p.name as permission_name 
                FROM menus m 
                LEFT JOIN permissions p ON m.required_permission_id = p.id
                WHERE m.type = :type AND m.is_active = 1";
        
        $params = ['type' => $type];

        if (!in_array($role, ['account_admin', 'super_admin'])) {
            $sql .= " AND (m.account_id IS NULL";
            if ($accountId) {
                $sql .= " OR m.account_id = :aid";
                $params['aid'] = $accountId;
            }
            $sql .= ")";
        }

        $sql .= " ORDER BY position ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $allMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $currentPath = $_SERVER['REQUEST_URI'];

        // Process filtering and active state
        $filtered = [];
        foreach ($allMenus as $item) {
            // Set active state for all visible menus
            $url = $item['url'] ?? '';
            $item['is_active'] = ($url !== '#' && $url !== '' && strpos($currentPath, (string)$url) === 0);

            // 1. Super Admin passes everything
            if (in_array($role, ['account_admin', 'super_admin'])) {
                $filtered[] = $item;
                continue;
            }

            // 2. Visibility checks
            $show = false;
            if ($item['visibility'] === 'public') {
                $show = true;
            } elseif ($role) {
                if ($item['visibility'] === 'admin') {
                    if (in_array($role, ['admin', 'account_admin', 'super_admin'])) {
                        $show = true;
                    }
                } else {
                    // authenticated or private (if matched role)
                    $show = true;
                }
            }

            // 3. Granular Permission Check
            if ($show && $item['permission_name']) {
                if (!RbacService::can($item['permission_name'])) {
                    $show = false;
                }
            }

            if ($show) {
                $filtered[] = $item;
            }
        }

        return $this->buildTree($filtered);
    }

    private function buildTree(array $elements, $parentId = null): array
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
