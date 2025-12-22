<?php

namespace OmniPOS\Widgets;

use OmniPOS\Core\Database;
use OmniPOS\Services\LocalizationService;

class ShopWidget extends BaseWidget
{
    public function render(array $props = []): string
    {
        $pdo = Database::connect();
        
        // Logic from ShopController::index
        $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 LIMIT 12");
        $stmt->execute();
        $products = $stmt->fetchAll();

        $searchQuery = $props['searchQuery'] ?? '';
        
        return $this->view->render('widgets/shop_catalog', [
            'products' => $products,
            'currentPage' => 1,
            'totalPages' => 1,
            'searchQuery' => $searchQuery,
            'widgetMode' => true
        ]);
    }
}
