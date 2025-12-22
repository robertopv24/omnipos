<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;

class PlatformAccountController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Solo Super Admin
        if (!in_array(Session::get('role'), ['account_admin', 'super_admin'])) {
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Listar todos las cuentas/clientes
     */
    public function index(Request $request, Response $response)
    {
        $this->view->setLayout('admin');
        
        $pdo = Database::connect();
        // Obtener cuentas (usuarios admin agrupados por account_id)
        // Nota: En una arquitectura ideal habría tabla 'accounts', aquí inferimos por usuario 'admin' principal
        $clients = $pdo->query("
            SELECT u.account_id, u.name as owner_name, u.email, u.created_at, 
            (SELECT COUNT(*) FROM businesses b WHERE b.account_id = u.account_id) as business_count
            FROM users u 
            WHERE u.role = 'admin'
            ORDER BY u.created_at DESC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('platform/accounts/index', [
            'title' => 'Gestión de Clientes',
            'clients' => $clients
        ]);
    }
}
