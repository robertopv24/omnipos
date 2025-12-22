<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\ManufacturedProduct;
use OmniPOS\Models\Recipe;
use OmniPOS\Services\MenuService;
use OmniPOS\Core\Session;

class ManufactureController extends Controller
{
    protected ManufacturedProduct $manufacturedModel;

    public function __construct()
    {
        parent::__construct();
        $this->manufacturedModel = new ManufacturedProduct();
    }

    public function recipes(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $this->view->setLayout('admin');
        $products = $this->manufacturedModel->all();

        return $this->render('manufacture/recipes', [
            'title' => 'Gestión de Recetas (Manufactura)',
            'products' => $products
        ]);
    }

    public function createRecipe(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $this->view->setLayout('admin');
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM raw_materials WHERE business_id = :bid");
        $stmt->execute(['bid' => Session::get('business_id')]);
        $materials = $stmt->fetchAll();

        return $this->render('manufacture/create_recipe', [
            'title' => 'Nueva Receta',
            'materials' => $materials
        ]);
    }

    public function storeRecipe(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $name = $request->get('name');
        $unit = $request->get('unit');
        $materials = $_POST['materials'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        $pdo = \OmniPOS\Core\Database::connect();
        $pdo->beginTransaction();

        try {
            $id = \OmniPOS\Core\Database::connect()->query("SELECT UUID()")->fetchColumn();
            $this->manufacturedModel->create([
                'id' => $id,
                'name' => $name,
                'unit' => $unit
            ]);

            foreach ($materials as $index => $materialId) {
                if (empty($materialId))
                    continue;

                $recipe = new Recipe();
                $recipe->create([
                    'manufactured_product_id' => $id,
                    'raw_material_id' => $materialId,
                    'quantity_required' => $quantities[$index] ?? 0
                ]);
            }

            $pdo->commit();
            $response->redirect('/manufacture/recipes');

        } catch (\Exception $e) {
            $pdo->rollBack();
            $response->redirect('/manufacture/recipes/create');
        }
    }

    public function createOrder(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $this->view->setLayout('admin');
        
        $id = $request->get('product_id');

        if (!$id) {
            \OmniPOS\Core\Session::flash('error', 'Seleccione un producto para fabricar.');
            return $response->redirect('/manufacture/recipes');
        }

        $product = $this->manufacturedModel->find($id);

        if (!$product) {
            \OmniPOS\Core\Session::flash('error', 'Producto no encontrado.');
            return $response->redirect('/manufacture/recipes');
        }

        return $this->render('manufacture/create_order', [
            'title' => 'Nueva Orden de Producción',
            'product' => $product
        ]);
    }

    public function storeOrder(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $productId = $request->get('manufactured_product_id');
        $quantity = (float) $request->get('quantity');

        // Validar entrada
        if ($quantity <= 0) {
            Session::flash('error', 'La cantidad debe ser mayor a cero');
            return $response->redirect('/manufacture/orders/create?product_id=' . $productId);
        }

        try {
            $service = new \OmniPOS\Services\ProductionService();
            $result = $service->processOrder($productId, $quantity, Session::get('user_id'));

            if ($result['success']) {
                Session::flash('success', 'Orden de producción procesada exitosamente');
                $response->redirect('/manufacture/recipes');
            } else {
                Session::flash('error', $result['message'] ?? 'Error al procesar orden de producción');
                $response->redirect('/manufacture/orders/create?product_id=' . $productId);
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error al procesar orden: ' . $e->getMessage());
            $response->redirect('/manufacture/orders/create?product_id=' . $productId);
        }
    }

    public function editRecipe(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $id = $request->get('id');
        $product = $this->manufacturedModel->find($id);

        if (!$product) {
            Session::flash('error', 'Producto no encontrado');
            return $response->redirect('/manufacture/recipes');
        }

        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM production_recipes WHERE manufactured_product_id = :id");
        $stmt->execute(['id' => $id]);
        $recipeMaterials = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM raw_materials WHERE business_id = :bid");
        $stmt->execute(['bid' => Session::get('business_id')]);
        $materials = $stmt->fetchAll();

        $this->view->setLayout('admin');

        return $this->render('manufacture/edit_recipe', [
            'title' => 'Editar Receta: ' . $product['name'],
            'product' => $product,
            'recipeMaterials' => $recipeMaterials,
            'materials' => $materials
        ]);
    }

    public function updateRecipe(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $id = $request->get('id');
        $name = $request->get('name');
        $unit = $request->get('unit');
        $materials = $_POST['materials'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        $pdo = \OmniPOS\Core\Database::connect();
        $pdo->beginTransaction();

        try {
            // 1. Actualizar datos básicos
            $this->manufacturedModel->update($id, [
                'name' => $name,
                'unit' => $unit
            ]);

            // 2. Limpiar receta vieja
            $pdo->prepare("DELETE FROM production_recipes WHERE manufactured_product_id = :id")
                ->execute(['id' => $id]);

            // 3. Insertar nueva receta
            foreach ($materials as $index => $materialId) {
                if (empty($materialId)) continue;

                $recipe = new Recipe();
                $recipe->create([
                    'manufactured_product_id' => $id,
                    'raw_material_id' => $materialId,
                    'quantity_required' => $quantities[$index] ?? 0
                ]);
            }

            $pdo->commit();
            Session::flash('success', 'Receta actualizada correctamente');
            $response->redirect('/manufacture/recipes');

        } catch (\Exception $e) {
            $pdo->rollBack();
            Session::flash('error', 'Error al actualizar receta: ' . $e->getMessage());
            $response->redirect('/manufacture/recipes/edit?id=' . $id);
        }
    }

    public function deleteRecipe(Request $request, Response $response)
    {
        $this->checkPermission('manage_manufacture');
        $id = $request->get('id');
        
        try {
            // El modelo se encarga de las validaciones de pertenencia
            $this->manufacturedModel->delete($id);
            Session::flash('success', 'Producto y receta eliminados');
        } catch (\Exception $e) {
            Session::flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }

        $response->redirect('/manufacture/recipes');
    }
}
