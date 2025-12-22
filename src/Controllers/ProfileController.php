<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Models\User;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;

class ProfileController extends Controller
{
    protected User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(Request $request, Response $response)
    {
        $this->view->setLayout('admin');
        $userId = Session::get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $response->redirect('/login');
        }

        return $this->render('auth/profile', [
            'title' => 'Mi Perfil',
            'user' => $user
        ]);
    }

    public function update(Request $request, Response $response)
    {
        $userId = Session::get('user_id');
        $data = $request->all();

        try {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email']
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $this->userModel->update($userId, $updateData);
            Session::set('user_name', $data['name']);
            
            Session::flash('success', 'Perfil actualizado correctamente');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al actualizar: ' . $e->getMessage());
        }

        $response->redirect('/profile');
    }
}
