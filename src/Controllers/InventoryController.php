<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Product;
use OmniPOS\Models\RawMaterial;
use OmniPOS\Services\InventoryService;
use OmniPOS\Core\Session;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct()
    {
        parent::__construct();
        $this->inventoryService = new InventoryService();
    }

    public function adjust(Request $request, Response $response)
    {
        $this->checkPermission('manage_inventory');
        $this->view->setLayout('admin');

        $productModel = new Product();
        $rawMaterialModel = new RawMaterial();

        $products = $productModel->all();
        $rawMaterials = $rawMaterialModel->all();

        return $this->render('inventory/adjust', [
            'title' => 'Ajuste de Inventario',
            'products' => $products,
            'rawMaterials' => $rawMaterials
        ]);
    }

    public function processAdjustment(Request $request, Response $response)
    {
        $this->checkPermission('manage_inventory');
        $data = $request->all();

        if (empty($data['item_id']) || empty($data['quantity']) || empty($data['type'])) {
            Session::flash('error', 'Todos los campos son obligatorios');
            return $response->redirect('/inventory/adjust');
        }

        try {
            $parts = explode(':', $data['item_id']);
            $itemType = $parts[0]; // 'product' or 'raw_material'
            $itemId = $parts[1];

            $this->inventoryService->adjustStock(
                $itemType,
                $itemId,
                (float)$data['quantity'],
                $data['type'],
                $data['notes'] ?? 'Ajuste manual'
            );

            Session::flash('success', 'Ajuste de inventario procesado exitosamente');
            $response->redirect('/inventory/adjust');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al procesar ajuste: ' . $e->getMessage());
            $response->redirect('/inventory/adjust');
        }
    }
}
