<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Product;
use OmniPOS\Models\ManufacturedProduct;
use OmniPOS\Services\MenuService;
use OmniPOS\Services\UploadService;
use OmniPOS\Core\Session;

class ProductController extends Controller
{
    protected Product $productModel;
    protected UploadService $uploadService;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->uploadService = new UploadService('products');
    }

    public function index(Request $request, Response $response)
    {
        $this->view->setLayout('admin');
        $products = $this->productModel->all();

        return $this->render('products/index', [
            'title' => 'Gestión de Productos',
            'products' => $products
        ]);
    }

    public function create(Request $request, Response $response)
    {
        $this->checkPermission('manage_products');
        $this->view->setLayout('admin');

        $manufacturedModel = new ManufacturedProduct();
        $manufactured = $manufacturedModel->all();

        return $this->render('products/create', [
            'title' => 'Nuevo Producto',
            'manufacturedProducts' => $manufactured
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $this->checkPermission('manage_products');
        $data = $request->all();

        // Validar datos de entrada
        $validator = new \OmniPOS\Core\Validator($data, [
            'name' => 'required|min_length:3|max_length:100',
            'price_usd' => 'required|numeric|positive',
            'price_ves' => 'required|numeric|positive',
            'stock' => 'numeric|min:0',
            'min_stock' => 'numeric|min:0'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/products/create');
        }

        try {
            // Limpiar linked_manufactured_id si está vacío
            if (empty($data['linked_manufactured_id'])) {
                unset($data['linked_manufactured_id']);
            }

            // Manejar precios (asegurar que sean décimales)
            $data['price_usd'] = $data['price_usd'] ?? 0;
            $data['price_ves'] = $data['price_ves'] ?? 0;
            $data['stock'] = $data['stock'] ?? 0;
            $data['min_stock'] = $data['min_stock'] ?? 5;
            $data['category_type'] = $data['category_type'] ?? 'resale';
            $data['kitchen_station'] = $data['kitchen_station'] ?? 'kitchen';
            $data['packaging_cost'] = $data['packaging_cost'] ?? 0;
            $data['is_featured_menu'] = isset($data['is_featured_menu']) ? 1 : 0;

            // Manejar imagen
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $filename = $this->uploadService->upload($_FILES['image']);
                if ($filename) {
                    $data['image_url'] = '/uploads/products/' . $filename;
                }
            }

            $this->productModel->create($data);
            Session::flash('success', 'Producto creado exitosamente');
            $response->redirect('/products');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al crear producto: ' . $e->getMessage());
            $response->redirect('/products/create');
        }
    }

    public function edit(Request $request, Response $response)
    {
        $this->checkPermission('manage_products');
        $this->view->setLayout('admin');
        $id = $request->get('id');
        $product = $this->productModel->find($id);

        if (!$product) {
            Session::flash('error', 'Producto no encontrado');
            return $response->redirect('/products');
        }

        $manufacturedModel = new ManufacturedProduct();
        $manufactured = $manufacturedModel->all();

        return $this->render('products/edit', [
            'title' => 'Editar Producto',
            'product' => $product,
            'manufacturedProducts' => $manufactured
        ]);
    }

    public function update(Request $request, Response $response)
    {
        $this->checkPermission('manage_products');
        $id = $request->get('id');
        $data = $request->all();
        $product = $this->productModel->find($id);

        if (!$product) {
            Session::flash('error', 'Producto no encontrado');
            return $response->redirect('/products');
        }

        // Validar datos de entrada
        $validator = new \OmniPOS\Core\Validator($data, [
            'name' => 'required|min_length:3|max_length:100',
            'price_usd' => 'required|numeric|positive',
            'price_ves' => 'required|numeric|positive',
            'stock' => 'numeric|min:0',
            'min_stock' => 'numeric|min:0'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/products/edit?id=' . $id);
        }

        try {
            // Limpiar linked_manufactured_id si está vacío
            if (empty($data['linked_manufactured_id'])) {
                $data['linked_manufactured_id'] = null;
            }

            // Precios y categoría
            $data['price_usd'] = $data['price_usd'] ?? 0;
            $data['price_ves'] = $data['price_ves'] ?? 0;
            $data['category_type'] = $data['category_type'] ?? 'resale';
            $data['kitchen_station'] = $data['kitchen_station'] ?? 'kitchen';
            $data['packaging_cost'] = $data['packaging_cost'] ?? 0;
            $data['is_featured_menu'] = isset($data['is_featured_menu']) ? 1 : 0;

            // Manejar imagen nueva
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                // Eliminar anterior si existe
                if ($product['image_url']) {
                    $oldFile = basename($product['image_url']);
                    $this->uploadService->delete($oldFile);
                }

                $filename = $this->uploadService->upload($_FILES['image']);
                if ($filename) {
                    $data['image_url'] = '/uploads/products/' . $filename;
                }
            }

            $this->productModel->update($id, $data);
            Session::flash('success', 'Producto actualizado exitosamente');
            $response->redirect('/products');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al actualizar producto: ' . $e->getMessage());
            $response->redirect('/products/edit?id=' . $id);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $this->checkPermission('manage_products');
        $id = $request->get('id');
        
        try {
            $product = $this->productModel->find($id);

            if (!$product) {
                Session::flash('error', 'Producto no encontrado');
                return $response->redirect('/products');
            }

            if ($product['image_url']) {
                $filename = basename($product['image_url']);
                $this->uploadService->delete($filename);
            }

            $this->productModel->delete($id);
            Session::flash('success', 'Producto eliminado exitosamente');
            $response->redirect('/products');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al eliminar producto: ' . $e->getMessage());
            $response->redirect('/products');
        }
    }
}
