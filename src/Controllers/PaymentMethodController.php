<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\PaymentMethod;
use OmniPOS\Core\Session;

class PaymentMethodController extends Controller
{
    protected PaymentMethod $paymentMethodModel;

    public function __construct()
    {
        parent::__construct();
        $this->paymentMethodModel = new PaymentMethod();
    }

    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $this->view->setLayout('admin');

        $methods = $this->paymentMethodModel->all();

        return $this->render('settings/payment_methods', [
            'title' => 'Métodos de Pago',
            'methods' => $methods
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $data = $request->all();

        if (empty($data['name']) || empty($data['currency'])) {
            Session::flash('error', 'Nombre y moneda son obligatorios');
            return $response->redirect('/settings/payment-methods');
        }

        try {
            $this->paymentMethodModel->create([
                'name' => $data['name'],
                'currency' => $data['currency'],
                'type' => $data['type'] ?? 'cash',
                'is_active' => 1
            ]);

            Session::flash('success', 'Método de pago agregado');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al agregar método: ' . $e->getMessage());
        }

        $response->redirect('/settings/payment-methods');
    }

    public function toggle(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $id = $request->get('id');
        $method = $this->paymentMethodModel->find($id);

        if ($method) {
            $this->paymentMethodModel->update($id, [
                'is_active' => $method['is_active'] ? 0 : 1
            ]);
            Session::flash('success', 'Estado actualizado');
        }

        $response->redirect('/settings/payment-methods');
    }

    public function delete(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $id = $request->get('id');

        try {
            $this->paymentMethodModel->delete($id);
            Session::flash('success', 'Método eliminado');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $response->redirect('/settings/payment-methods');
    }
}
