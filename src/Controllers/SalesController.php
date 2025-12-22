<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Product;
use OmniPOS\Services\SalesService;
use OmniPOS\Core\Session;

class SalesController extends Controller
{
    protected Product $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }

    public function index(Request $request, Response $response)
    {
        $this->checkPermission('view_sales');
        $this->view->setLayout('admin');
        
        $page = $request->get('page', 1);
        $perPage = 15;
        $businessId = Session::get('business_id');

        // Basic query for sales history
        $salesService = new SalesService();
        $sales = $salesService->getSalesHistory($businessId, $page, $perPage);

        return $this->render('sales/index', [
            'title' => 'Historial de Ventas',
            'sales' => $sales
        ]);
    }

    public function show(Request $request, Response $response)
    {
        $this->checkPermission('view_sales');
        $this->view->setLayout('admin');
        
        $id = $request->get('id');
        $businessId = Session::get('business_id');

        $salesService = new SalesService();
        $sale = $salesService->getOrderDetails($id, $businessId);

        if (!$sale) {
            Session::flash('error', 'Venta no encontrada');
            return $response->redirect('/sales');
        }

        return $this->render('sales/show', [
            'title' => 'Detalle de Venta #' . substr($sale['id'], 0, 8),
            'sale' => $sale
        ]);
    }



    public function edit(Request $request, Response $response)
    {
        $this->checkPermission('manage_sales');
        $this->view->setLayout('admin');
        
        $id = $request->get('id');
        $businessId = Session::get('business_id');

        $salesService = new SalesService();
        $sale = $salesService->getOrderDetails($id, $businessId);

        if (!$sale) {
            Session::flash('error', 'Venta no encontrada');
            return $response->redirect('/sales');
        }

        $clientModel = new \OmniPOS\Models\Client();
        $clients = $clientModel->all();

        return $this->render('sales/edit', [
            'title' => 'Editar Venta',
            'sale' => $sale,
            'clients' => $clients
        ]);
    }

    public function update(Request $request, Response $response)
    {
        $this->checkPermission('manage_sales');
        $id = $request->get('id');
        $data = $request->all();
        $businessId = Session::get('business_id');

        try {
            // Validate status change rules if needed
            // For now, allow simple updates
            $orderModel = new \OmniPOS\Models\Order();
            $orderModel->update($id, [
                'client_id' => !empty($data['client_id']) ? $data['client_id'] : null,
                'status' => $data['status']
            ]);

            Session::flash('success', 'Venta actualizada exitosamente');
            $response->redirect('/sales/show?id=' . $id);
        } catch (\Exception $e) {
            Session::flash('error', 'Error al actualizar venta: ' . $e->getMessage());
            $response->redirect('/sales/edit?id=' . $id);
        }
    }

    public function pos(Request $request, Response $response)
    {
        $this->checkPermission('view_pos');
        $this->view->setLayout('admin');

        $businessId = Session::get('business_id');
        if (!$businessId) {
             Session::flash('error', 'Sesión de negocio no encontrada.');
             return $response->redirect('/account/businesses');
        }

        // Paginación para el POS
        $page = (int) $request->get('page', 1);
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $pdo = \OmniPOS\Core\Database::connect();
        
        // Contar productos totales
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE business_id = :bid AND category_type = 'resale'");
        $countStmt->execute(['bid' => $businessId]);
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $limit);

        // Obtener productos paginados
        $stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = :bid AND category_type = 'resale' LIMIT $limit OFFSET $offset");
        $stmt->execute(['bid' => $businessId]);
        $products = $stmt->fetchAll();

        $accountingService = new \OmniPOS\Services\AccountingService();
        $exchangeRate = $accountingService->getExchangeRate($businessId);

        // Obtener métodos de pago activos
        $stmtMethods = $pdo->prepare("SELECT * FROM payment_methods WHERE business_id = :bid AND is_active = 1");
        $stmtMethods->execute(['bid' => $businessId]);
        $paymentMethods = $stmtMethods->fetchAll();

        return $this->render('sales/pos', [
            'title' => 'Punto de Venta (POS)',
            'products' => $products,
            'paymentMethods' => $paymentMethods,
            'exchangeRate' => $exchangeRate,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function searchProducts(Request $request, Response $response)
    {
        try {
            $query = (string) $request->get('q', '');
            $page = (int) $request->get('page', 1);
            $limit = 6;
            $offset = ($page - 1) * $limit;
            $businessId = Session::get('business_id');
            
            if (!$businessId) {
                return $response->json(['products' => [], 'totalPages' => 0]);
            }

            $pdo = \OmniPOS\Core\Database::connect();

            // Contar total para esta búsqueda
            $countSql = "SELECT COUNT(*) FROM products 
                        WHERE business_id = ? 
                        AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) 
                        AND category_type = 'resale'";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute([$businessId, "%{$query}%", "%{$query}%", "%{$query}%"]);
            $totalItems = $countStmt->fetchColumn();
            $totalPages = ceil($totalItems / $limit);

            $sql = "SELECT * FROM products 
                    WHERE business_id = ? 
                    AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) 
                    AND category_type = 'resale'
                    LIMIT $limit OFFSET $offset";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $businessId,
                "%{$query}%",
                "%{$query}%",
                "%{$query}%"
            ]);

            return $response->json([
                'products' => $stmt->fetchAll(),
                'totalPages' => $totalPages,
                'currentPage' => $page
            ]);
        } catch (\Exception $e) {
            return $response->json(['products' => [], 'totalPages' => 0, 'error' => $e->getMessage()]);
        }
    }

    public function searchClients(Request $request, Response $response)
    {
        try {
            $query = (string) $request->get('q', '');
            $businessId = Session::get('business_id');

            if (!$businessId) {
                return $response->json([]);
            }

            $clientModel = new \OmniPOS\Models\Client();
            $clients = $clientModel->search($query);

            return $response->json($clients);
        } catch (\Exception $e) {
            return $response->json([]);
        }
    }

    public function checkout(Request $request, Response $response)
    {
        $this->checkPermission('view_pos');
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar que haya items
            if (empty($data['items']) || count($data['items']) == 0) {
                return $response->json(['success' => false, 'message' => 'El carrito está vacío']);
            }

            // Validar total
            if (!isset($data['total']) || $data['total'] <= 0) {
                return $response->json(['success' => false, 'message' => 'El total de la venta debe ser mayor a cero']);
            }

            // 1. Validar autorización de supervisor para créditos o beneficios
            if ($data['payment_type'] === 'credit' || !empty($data['is_benefit'])) {
                $supervisorId = \OmniPOS\Services\RbacService::validateSupervisor($data['supervisor_code'] ?? '');
                if (!$supervisorId) {
                    return $response->json(['success' => false, 'message' => 'Código de supervisor inválido o requerido para esta operación.']);
                }
                $data['authorized_by'] = $supervisorId;
            }

            // 2. Buscar sesión de caja abierta
            $pdo = \OmniPOS\Core\Database::connect();
            $stmt = $pdo->prepare("SELECT id FROM cash_sessions WHERE user_id = :uid AND status = 'open' LIMIT 1");
            $stmt->execute(['uid' => Session::get('user_id')]);
            $session = $stmt->fetch();

            if (!$session) {
                return $response->json(['success' => false, 'message' => 'No tienes una sesión de caja abierta.']);
            }

            $data['cash_session_id'] = $session['id'];

            $salesService = new SalesService();
            $result = $salesService->processSale($data);

            return $response->json($result);
        } catch (\Exception $e) {
            return $response->json(['success' => false, 'message' => 'Error al procesar venta: ' . $e->getMessage()]);
        }
    }
}
