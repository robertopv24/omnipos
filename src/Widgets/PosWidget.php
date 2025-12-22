<?php

namespace OmniPOS\Widgets;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use OmniPOS\Services\SalesService;

class PosWidget extends BaseWidget
{
    public function render(array $props = []): string
    {
        // 1. Fetch Data required for POS
        // Logic copied/adapted from SalesController::pos
        $pdo = Database::connect();
        $accountId = Session::get('account_id');
        
        $products = [];
        if ($accountId) {
             $stmt = $pdo->prepare("SELECT * FROM products WHERE account_id = ? AND is_active = 1 LIMIT 50"); // Limit for widget perf
             $stmt->execute([$accountId]);
             $products = $stmt->fetchAll();
        }

        $paymentMethods = [];
        if ($accountId) {
             $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE account_id = ? AND is_active = 1");
             $stmt->execute([$accountId]);
             $paymentMethods = $stmt->fetchAll();
        }

        // Mock pagination for initial load
        $currentPage = 1;
        $totalPages = 1; 

        $exchangeRate = 35.5; // TODO: Get from service

        // 2. Render the View
        // We use a specific widget view, or reuse the existing one if compatible
        // Ideally we refactor `src/Views/sales/pos.php` to be partial-friendly
        // For now, we assume we use a specialized widget view
        
        return $this->view->render('widgets/pos_terminal', [
            'products' => $products,
            'paymentMethods' => $paymentMethods,
            'exchangeRate' => $exchangeRate,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'widgetMode' => true // Flag to tell view to behave as widget
        ]);
    }
}
