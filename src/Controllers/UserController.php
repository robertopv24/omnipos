<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\User;
use OmniPOS\Services\MenuService;
use OmniPOS\Core\Session;

class UserController extends Controller
{
    protected User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_users');
        $this->view->setLayout('admin');

        $role = Session::get('role');
        $accountId = Session::get('account_id');

        if (in_array($role, ['account_admin', 'super_admin'])) {
            $users = $this->userModel->all();
        } else {
            $users = $this->userModel->allByAccount($accountId);
        }

        return $this->render('users/index', [
            'title' => 'Gestión de Usuarios',
            'users' => $users
        ]);
    }

    public function create(Request $request, Response $response)
    {
        $this->checkPermission('manage_users');
        $this->view->setLayout('admin');

        $activeAccountId = Session::get('impersonated_account_id') ?? Session::get('account_id');
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT id, name FROM businesses WHERE account_id = :account_id");
        $stmt->execute(['account_id' => $activeAccountId]);
        $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('users/create', [
            'title' => 'Agregar Usuario',
            'businesses' => $businesses
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $this->checkPermission('manage_users');
        $data = $request->all();

        // Hash de contraseña
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Asignar IDs de contexto
        $activeAccountId = Session::get('impersonated_account_id') ?? Session::get('account_id');
        $data['business_id'] = $data['business_id'] ?? Session::get('business_id');
        $data['account_id'] = $activeAccountId;

        $this->userModel->create($data);

        $response->redirect('/users');
    }

    public function edit(Request $request, Response $response)
    {
        $this->checkPermission('manage_users');
        $this->view->setLayout('admin');
        $id = $request->get('id');
        $user = $this->userModel->find($id);

        $activeAccountId = Session::get('impersonated_account_id') ?? Session::get('account_id');
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT id, name FROM businesses WHERE account_id = :account_id");
        $stmt->execute(['account_id' => $activeAccountId]);
        $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('users/edit', [
            'title' => 'Editar Usuario',
            'user' => $user,
            'businesses' => $businesses
        ]);
    }

    public function update(Request $request, Response $response)
    {
        $this->checkPermission('manage_users');
        $id = $request->get('id');
        $data = $request->all();

        // No actualizar contraseña si viene vacía
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $data);
        $response->redirect('/users');
    }

    public function delete(Request $request, Response $response)
    {
        $this->checkPermission('manage_users');
        $id = $request->get('id');
        $this->userModel->delete($id);
        $response->redirect('/users');
    }
}
