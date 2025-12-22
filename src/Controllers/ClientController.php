<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Client;
use OmniPOS\Core\Session;

class ClientController extends Controller
{
    protected Client $clientModel;

    public function __construct()
    {
        parent::__construct();
        $this->clientModel = new Client();
    }

    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_clients');
        $this->view->setLayout('admin');
        
        $clients = $this->clientModel->all();

        return $this->render('clients/index', [
            'title' => 'Gestión de Clientes',
            'clients' => $clients
        ]);
    }

    public function create(Request $request, Response $response)
    {
        $this->checkPermission('manage_clients');
        $this->view->setLayout('admin');

        return $this->render('clients/create', [
            'title' => 'Nuevo Cliente'
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $this->checkPermission('manage_clients');
        $data = $request->all();

        // Validation
        if (empty($data['name'])) {
            Session::flash('error', 'El nombre es obligatorio');
            return $response->redirect('/clients/create');
        }

        try {
            $this->clientModel->create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'address' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'is_active' => 1
            ]);

            Session::flash('success', 'Cliente creado exitosamente');
            $response->redirect('/clients');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al crear cliente: ' . $e->getMessage());
            $response->redirect('/clients/create');
        }
    }

    public function edit(Request $request, Response $response)
    {
        $this->checkPermission('manage_clients');
        $this->view->setLayout('admin');
        $id = $request->get('id');

        $client = $this->clientModel->find($id);

        if (!$client) {
            Session::flash('error', 'Cliente no encontrado');
            return $response->redirect('/clients');
        }

        return $this->render('clients/edit', [
            'title' => 'Editar Cliente',
            'client' => $client
        ]);
    }

    public function update(Request $request, Response $response)
    {
        $this->checkPermission('manage_clients');
        $id = $request->get('id');
        $data = $request->all();

        try {
            $this->clientModel->update($id, [
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'address' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);

            Session::flash('success', 'Cliente actualizado exitosamente');
            $response->redirect('/clients');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al actualizar cliente: ' . $e->getMessage());
            $response->redirect('/clients/edit?id=' . $id);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $this->checkPermission('manage_clients');
        $id = $request->get('id');

        try {
            $this->clientModel->delete($id);
            Session::flash('success', 'Cliente eliminado exitosamente');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al eliminar cliente: ' . $e->getMessage());
        }
        
        $response->redirect('/clients');
    }
}
