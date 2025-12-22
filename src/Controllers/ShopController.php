<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Product;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;

class ShopController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        $businessId = Session::get('business_id');
        $pdo = Database::connect();

        if (!$businessId) {
            // Demo mode: pick the first available business
            $stmt = $pdo->prepare("SELECT id FROM businesses LIMIT 1");
            $stmt->execute();
            $businessId = $stmt->fetchColumn();

            if (!$businessId) {
                return $response->redirect('/');
            }
            // Persistence for guest browsing
            Session::set('business_id', $businessId);
        }

        // Búsqueda y Paginación
        $query = $request->get('q', '');
        $page = (int) $request->get('page', 1);
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $searchParam = "%{$query}%";

        // Contar total para paginación
        $countSql = "SELECT COUNT(*) FROM products WHERE business_id = ? AND category_type = 'resale'";
        $countParams = [$businessId];
        
        if ($query) {
            // Contar total para esta búsqueda (incluyendo SKU)
            $countSql = "SELECT COUNT(*) FROM products 
                        WHERE business_id = ? 
                        AND (name LIKE ? OR description LIKE ? OR sku LIKE ?) 
                        AND category_type = 'resale'";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute([$businessId, $searchParam, $searchParam, $searchParam]);
        } else {
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($countParams);
        }
        
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $limit);

        // Obtener productos de la página actual
        $sql = "SELECT * FROM products WHERE business_id = ? AND category_type = 'resale'";
        $params = [$businessId];
        
        if ($query) {
            $sql .= " AND (name LIKE ? OR description LIKE ? OR sku LIKE ?)";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        $sql .= " LIMIT $limit OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        return $this->render('shop/index', [
            'title' => 'Tienda en Línea',
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $query
        ]);
    }

    public function cart(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        $cart = Session::get('cart', []);

        return $this->render('shop/cart', [
            'title' => 'Mi Carrito',
            'cart' => $cart
        ]);
    }

    public function checkout(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return $response->redirect('/shop');
        }

        return $this->render('shop/checkout', [
            'title' => 'Finalizar Compra',
            'cart' => $cart
        ]);
    }

    public function orderStatus(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        $orderId = $request->get('id');
        
        // Lógica para buscar el estado del pedido
        return $this->render('shop/order_status', [
            'title' => 'Estado de Pedido',
            'orderId' => $orderId
        ]);
    }

    public function addToCart(Request $request, Response $response)
    {
        $productId = $request->post('product_id');
        $quantity = $request->post('quantity', 1);

        $cart = Session::get('cart', []);
        
        // Simulación de lógica de carrito
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute(['id' => $productId]);
            $product = $stmt->fetch();
            
            if ($product) {
                $cart[$productId] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price_usd'],
                    'quantity' => $quantity,
                    'image' => $product['image_url']
                ];
            }
        }

        Session::set('cart', $cart);
        return $response->json(['success' => true, 'cart_count' => count($cart)]);
    }
}
