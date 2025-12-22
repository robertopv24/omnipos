<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Table;
use OmniPOS\Core\Session;
use OmniPOS\Services\MenuService;

class TableController extends Controller
{
    protected Table $tableModel;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel = new Table();
    }

    public function index(Request $request, Response $response)
    {
        $this->checkPermission('view_restoration');
        $this->view->setLayout('admin');
        $tables = $this->tableModel->getByBusiness(Session::get('business_id'));

        return $this->render('restoration/tables', [
            'title' => 'Gestión de Mesas',
            'tables' => $tables
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $this->checkPermission('view_restoration');
        $tableNumber = $request->get('table_number');
        $capacity = $request->get('capacity');
        $zone = $request->get('zone');

        try {
            $this->tableModel->create([
                'id' => \OmniPOS\Core\Database::connect()->query("SELECT UUID()")->fetchColumn(),
                'business_id' => Session::get('business_id'),
                'table_number' => $tableNumber,
                'capacity' => $capacity,
                'zone' => $zone,
                'status' => 'available'
            ]);
            Session::flash('success', 'Mesa agregada correctamente');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al agregar mesa: ' . $e->getMessage());
        }

        $response->redirect('/restoration/tables');
    }

    public function delete(Request $request, Response $response)
    {
        $this->checkPermission('view_restoration');
        $id = $request->get('id');
        try {
            $this->tableModel->delete($id);
            Session::flash('success', 'Mesa eliminada');
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }
        $response->redirect('/restoration/tables');
    }
}
