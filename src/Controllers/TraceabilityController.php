<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Database;
use OmniPOS\Services\MenuService;
use OmniPOS\Core\Session;

class TraceabilityController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_inventory');
        $this->view->setLayout('admin');
        $productId = $request->get('product_id');

        $pdo = Database::connect();

        // 1. Obtener información del producto
        $stmt = $pdo->prepare("SELECT name, sku FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();

        if (!$product) {
            return $response->redirect('/products');
        }

        // 2. Obtener lotes del producto
        $stmt = $pdo->prepare("SELECT * FROM inventory_batches WHERE item_id = :id ORDER BY received_at DESC");
        $stmt->execute(['id' => $productId]);
        $batches = $stmt->fetchAll();

        // 3. Obtener movimientos de trazabilidad para este producto
        $stmt = $pdo->prepare("
            SELECT m.*, b.batch_number, u.name as user_name 
            FROM inventory_movements m
            JOIN inventory_batches b ON m.batch_id = b.id
            JOIN users u ON m.created_by = u.id
            WHERE b.item_id = :id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute(['id' => $productId]);
        $movements = $stmt->fetchAll();

        return $this->render('inventory/traceability', [
            'title' => 'Reporte de Trazabilidad: ' . $product['name'],
            'product' => $product,
            'batches' => $batches,
            'movements' => $movements
        ]);
    }
}
