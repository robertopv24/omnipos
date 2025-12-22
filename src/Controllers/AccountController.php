<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\Business;
use OmniPOS\Services\MenuService;
use OmniPOS\Core\Session;

class AccountController extends Controller
{
    protected Business $businessModel;

    public function __construct()
    {
        parent::__construct();
        $this->businessModel = new Business();
    }

    public function businesses(Request $request, Response $response)
    {
        $this->view->setLayout('admin');
        $role = Session::get('role');

        $pdo = \OmniPOS\Core\Database::connect();
        
        if (in_array($role, ['account_admin', 'super_admin'])) {
            // Super Admin ve todos los negocios de todos los clientes
            $stmt = $pdo->prepare("SELECT * FROM businesses ORDER BY account_id, name ASC");
            $stmt->execute();
        } else {
            // Usuarios normales solo ven sus negocios
            $stmt = $pdo->prepare("SELECT * FROM businesses WHERE account_id = :account_id");
            $stmt->execute(['account_id' => Session::get('account_id')]);
        }
        
        $businesses = $stmt->fetchAll();

        // Si solo hay uno y no tenemos business_id, seleccionarlo por defecto
        if (count($businesses) === 1 && !Session::get('business_id')) {
            Session::set('business_id', $businesses[0]['id']);
            Session::set('business_name', $businesses[0]['name']);
            $response->redirect('/dashboard');
        }

        return $this->render('account/businesses', [
            'title' => 'Mis Negocios',
            'businesses' => $businesses
        ]);
    }

    public function switch(Request $request, Response $response)
    {
        $businessId = $request->get('id');
        $role = Session::get('role');

        $pdo = \OmniPOS\Core\Database::connect();
        
        if (in_array($role, ['account_admin', 'super_admin'])) {
            // Super Admin puede saltar a cualquier negocio
            $stmt = $pdo->prepare("SELECT id, name, account_id FROM businesses WHERE id = :bid");
            $stmt->execute(['bid' => $businessId]);
        } else {
            // Usuarios normales solo a sus propios negocios
            $stmt = $pdo->prepare("SELECT id, name, account_id FROM businesses WHERE id = :bid AND account_id = :aid");
            $stmt->execute([
                'bid' => $businessId,
                'aid' => Session::get('account_id')
            ]);
        }

        $business = $stmt->fetch();
        if ($business) {
            Session::set('business_id', $businessId);
            Session::set('business_name', $business['name']);
            // Si el Super Admin entra a un negocio de otro cliente, actualizamos temporalmente account_id?
            // Generalmente es mejor mantener su account_id original pero saber que está "mimetizado".
            // Pero para reportes que filtran por account_id (como Master Dashboard), 
            // quizás queramos que vea los datos de ESE cliente.
            if (in_array($role, ['account_admin', 'super_admin'])) {
                Session::set('impersonated_account_id', $business['account_id']);
            }
            $response->redirect('/dashboard');
        } else {
            $response->redirect('/account/businesses?error=invalid_business');
        }
    }

    public function create(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $this->view->setLayout('admin');

        return $this->render('account/create_business', [
            'title' => 'Agregar Nuevo Negocio'
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $data = $request->all();
        $data['account_id'] = Session::get('account_id');

        if ($this->businessModel->create($data)) {
            $response->redirect('/account/businesses');
        } else {
            $response->redirect('/account/businesses/create?error=failed');
        }
    }
}
