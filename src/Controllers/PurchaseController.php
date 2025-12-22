<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Services\PurchaseService;
use OmniPOS\Services\MenuService;
use OmniPOS\Models\Supplier;
use OmniPOS\Models\Product;
use OmniPOS\Core\Database;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct()
    {
        parent::__construct();
        $this->purchaseService = new PurchaseService();
    }

    /**
     * Lista de órdenes de compra
     */
    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $this->view->setLayout('admin');

        $orders = $this->purchaseService->getPendingOrders(Session::get('business_id'));

        return $this->render('purchases/index', [
            'title' => 'Órdenes de Compra',
            'orders' => $orders
        ]);
    }

    /**
     * Formulario para crear nueva orden de compra
     */
    public function create(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $this->view->setLayout('admin');

        $supplierModel = new Supplier();
        $suppliers = $supplierModel->all();

        // Obtener productos separados por tipo
        $pdo = Database::connect();
        
        // Productos para reventa
        $stmt = $pdo->prepare("SELECT id, name, stock, min_stock, 'resale' as category_type FROM products WHERE business_id = :bid AND category_type = 'resale' ORDER BY name");
        $stmt->execute(['bid' => Session::get('business_id')]);
        $resaleProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Insumos operativos
        $stmt = $pdo->prepare("SELECT id, name, stock, min_stock, 'operational_supply' as category_type FROM products WHERE business_id = :bid AND category_type = 'operational_supply' ORDER BY name");
        $stmt->execute(['bid' => Session::get('business_id')]);
        $operationalSupplies = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Materias primas
        $stmt = $pdo->prepare("SELECT id, name, stock_quantity as stock FROM raw_materials WHERE business_id = :bid ORDER BY name");
        $stmt->execute(['bid' => Session::get('business_id')]);
        $rawMaterials = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('purchases/create', [
            'title' => 'Nueva Orden de Compra',
            'suppliers' => $suppliers,
            'resaleProducts' => $resaleProducts,
            'operationalSupplies' => $operationalSupplies,
            'rawMaterials' => $rawMaterials
        ]);
    }

    /**
     * Guardar nueva orden de compra
     */
    public function store(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $data = $request->all();

        // Validar
        $validator = new \OmniPOS\Core\Validator($data, [
            'supplier_id' => 'required',
            'payment_term_days' => 'required|min:0'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/purchases/create');
        }

        // Validar que haya items
        if (empty($data['items']) || !is_array($data['items']) || count($data['items']) == 0) {
            Session::flash('error', 'Debe agregar al menos un producto a la orden');
            return $response->redirect('/purchases/create');
        }

        // Validación profunda de items
        $pdo = Database::connect();
        foreach ($data['items'] as $index => $item) {
            // Validar campos requeridos
            if (empty($item['item_id']) || empty($item['quantity']) || empty($item['unit_cost'])) {
                Session::flash('error', "El item #" . ($index + 1) . " tiene campos incompletos.");
                return $response->redirect('/purchases/create');
            }

            // Validar valores positivos
            if ($item['quantity'] <= 0) {
                Session::flash('error', "La cantidad del item #" . ($index + 1) . " debe ser mayor a 0.");
                return $response->redirect('/purchases/create');
            }
            if ($item['unit_cost'] < 0) {
                Session::flash('error', "El costo del item #" . ($index + 1) . " no puede ser negativo.");
                return $response->redirect('/purchases/create');
            }

            // Validar existencia del ID en BD
            $table = ($item['item_type'] === 'raw_material') ? 'raw_materials' : 'products';
            $stmt = $pdo->prepare("SELECT id FROM {$table} WHERE id = :id AND business_id = :bid");
            $stmt->execute(['id' => $item['item_id'], 'bid' => Session::get('business_id')]);
            
            if (!$stmt->fetch()) {
                Session::flash('error', "El item seleccionado en la fila #" . ($index + 1) . " no existe o no pertenece a este negocio.");
                return $response->redirect('/purchases/create');
            }
        }

        try {
            $result = $this->purchaseService->createOrder($data);

            if ($result['success']) {
                Session::flash('success', 'Orden de compra #' . $result['order_number'] . ' creada exitosamente');
                $response->redirect('/purchases');
            } else {
                Session::flash('error', $result['message'] ?? 'Error al crear orden de compra');
                $response->redirect('/purchases/create');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            $response->redirect('/purchases/create');
        }
    }

    /**
     * Formulario de recepción de mercancía
     */
    public function receive(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $this->view->setLayout('admin');
        $orderId = $request->get('id');

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT po.*, s.name as supplier_name FROM purchase_orders po LEFT JOIN suppliers s ON po.supplier_id = s.id WHERE po.id = :id");
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            Session::flash('error', 'Orden de compra no encontrada');
            return $response->redirect('/purchases');
        }

        $items = $this->purchaseService->getOrderItems($orderId);

        return $this->render('purchases/receive', [
            'title' => 'Recibir Mercancía - Orden #' . $order['order_number'],
            'order' => $order,
            'items' => $items
        ]);
    }

    /**
     * Procesar recepción de mercancía
     */
    public function processReceive(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $orderId = $request->get('order_id');
        $receivedItems = $request->get('received') ?? [];

        try {
            $result = $this->purchaseService->receiveOrder($orderId, $receivedItems);

            if ($result['success']) {
                Session::flash('success', 'Mercancía recibida exitosamente. Estado: ' . $result['status']);
                $response->redirect('/purchases');
            } else {
                Session::flash('error', $result['message'] ?? 'Error al recibir mercancía');
                $response->redirect('/purchases/receive?id=' . $orderId);
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            $response->redirect('/purchases/receive?id=' . $orderId);
        }
    }

    /**
     * Gestión de proveedores
     */
    public function suppliers(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $this->view->setLayout('admin');

        $supplierModel = new Supplier();
        $suppliers = $supplierModel->all(); 

        return $this->render('suppliers/index', [
            'title' => 'Gestión de Proveedores',
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Formulario para crear proveedor
     */
    public function createSupplier(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $this->view->setLayout('admin');

        return $this->render('suppliers/create', [
            'title' => 'Nuevo Proveedor'
        ]);
    }

    /**
     * Guardar nuevo proveedor
     */
    public function storeSupplier(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $data = $request->all();

        // Validar
        $validator = new \OmniPOS\Core\Validator($data, [
            'name' => 'required|min_length:3|max_length:255',
            'email' => 'email'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/suppliers/create');
        }

        try {
            $supplierModel = new Supplier();
            $supplierModel->create($data); // create() adds business_id automatically via Model.php
            
            Session::flash('success', 'Proveedor creado exitosamente');
            $response->redirect('/suppliers');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al crear proveedor: ' . $e->getMessage());
            $response->redirect('/suppliers/create');
        }
    }
    /**
     * Formulario para editar proveedor
     */
    public function editSupplier(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $this->view->setLayout('admin');
        $id = $request->get('id');
        
        $supplierModel = new Supplier();
        $supplier = $supplierModel->find($id);

        if (!$supplier) {
            Session::flash('error', 'Proveedor no encontrado');
            return $response->redirect('/suppliers');
        }

        return $this->render('suppliers/edit', [
            'title' => 'Editar Proveedor',
            'supplier' => $supplier
        ]);
    }

    /**
     * Actualizar proveedor
     */
    public function updateSupplier(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $id = $request->get('id');
        $data = $request->all();

        $supplierModel = new Supplier();
        $supplier = $supplierModel->find($id);

        if (!$supplier) {
            Session::flash('error', 'Proveedor no encontrado');
            return $response->redirect('/suppliers');
        }

        // Validar
        $validator = new \OmniPOS\Core\Validator($data, [
            'name' => 'required|min_length:3|max_length:255',
            'email' => 'email'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/suppliers/edit?id=' . $id);
        }

        try {
            $supplierModel->update($id, $data);
            Session::flash('success', 'Proveedor actualizado exitosamente');
            $response->redirect('/suppliers');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al actualizar proveedor: ' . $e->getMessage());
            $response->redirect('/suppliers/edit?id=' . $id);
        }
    }

    /**
     * Eliminar proveedor
     */
    public function deleteSupplier(Request $request, Response $response)
    {
        $this->checkPermission('manage_purchases');
        $id = $request->get('id');
        
        $supplierModel = new Supplier();
        $supplier = $supplierModel->find($id);

        if (!$supplier) {
            Session::flash('error', 'Proveedor no encontrado');
            return $response->redirect('/suppliers');
        }

        try {
            $supplierModel->delete($id);
            Session::flash('success', 'Proveedor eliminado exitosamente');
            $response->redirect('/suppliers');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al eliminar proveedor: ' . $e->getMessage());
            $response->redirect('/suppliers');
        }
    }
}
