<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;
use OmniPOS\Services\MenuService;
use PDO;

class RestorationController extends Controller
{
    public function digitalMenu(Request $request, Response $response)
    {
        $this->view->setLayout('public'); // Layout sin sidebar para pantallas de salón
        $businessId = Session::get('business_id');

        $pdo = Database::connect();
        // Corregido: removido is_active que no existe en la tabla products
        $stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = :business_id AND is_featured_menu = 1");
        $stmt->execute(['business_id' => $businessId]);
        $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->render('restoration/digital_menu', [
            'title' => 'Menú Digital',
            'products' => $featuredProducts
        ]);
    }

    public function kdsData(Request $request, Response $response)
    {
        $this->checkPermission('view_restoration');
        $businessId = Session::get('business_id');
        $station = $request->get('station');

        $pdo = Database::connect();
        $sql = "SELECT oi.*, p.name as product_name, p.kitchen_station, SUBSTR(o.id, 1, 8) as order_number, o.created_at as order_time 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.business_id = :business_id 
                AND oi.status IN ('pending', 'preparing')";

        if ($station) {
            $sql .= " AND p.kitchen_station = :station";
        }

        $sql .= " ORDER BY o.created_at ASC";

        $stmt = $pdo->prepare($sql);
        $params = ['business_id' => $businessId];
        if ($station) $params['station'] = $station;

        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];
        foreach ($items as $item) {
            $orderId = $item['order_id'];
            if ($item['modifications']) {
                $item['modifications_data'] = json_decode($item['modifications'], true);
            }
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'order_number' => $item['order_number'],
                    'order_time' => $item['order_time'],
                    'items' => []
                ];
            }
            $orders[$orderId]['items'][] = $item;
        }

        return $response->json(array_values($orders));
    }

    public function kds(Request $request, Response $response)
    {
        $this->checkPermission('view_restoration');
        $this->view->setLayout('admin');
        $businessId = Session::get('business_id');
        $station = $request->get('station');

        $pdo = Database::connect();
        $sql = "SELECT oi.*, p.name as product_name, p.kitchen_station, SUBSTR(o.id, 1, 8) as order_number, o.created_at as order_time 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.business_id = :business_id 
                AND oi.status IN ('pending', 'preparing')";

        if ($station) {
            $sql .= " AND p.kitchen_station = :station";
        }

        $sql .= " ORDER BY o.created_at ASC";

        $stmt = $pdo->prepare($sql);
        $params = ['business_id' => $businessId];
        if ($station)
            $params['station'] = $station;

        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];
        foreach ($items as $item) {
            if ($item['modifications']) {
                $item['modifications_data'] = json_decode($item['modifications'], true);
            }
            $orders[$item['order_id']]['order_number'] = $item['order_number'];
            $orders[$item['order_id']]['order_time'] = $item['order_time'];
            $orders[$item['order_id']]['items'][] = $item;
        }

        return $this->render('restoration/kds', [
            'title' => 'Sistema de Pantallas de Cocina (KDS)',
            'orders' => $orders,
            'currentStation' => $station
        ]);
    }

    public function updateItemStatus(Request $request, Response $response)
    {
        $this->checkPermission('view_restoration');
        $itemId = $request->post('item_id');
        $status = $request->post('status');

        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE order_items SET status = :status WHERE id = :id");
        $success = $stmt->execute(['status' => $status, 'id' => $itemId]);

        return $response->json(['success' => $success]);
    }
}
