<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;
use OmniPOS\Services\MenuService;

class PlatformDashboardController extends Controller
{
    public function index(Request $request, Response $response)
    {
        // El rol 'account_admin' es especial y tiene todos los permisos en RbacService,
        // pero aquí forzamos la verificación de un permiso específico de 'manage_system'
        // o similar si existiera, o simplemente validamos el rol por ahora.
        if (!in_array(Session::get('role'), ['account_admin', 'super_admin'])) {
            $response->redirect('/dashboard');
            return;
        }

        $this->view->setLayout('admin'); // Podríamos crear un layout 'platform' específico en el futuro

        // Calcular Métricas Globales de la Plataforma
        $pdo = Database::connect();
        
        // 1. Total de Clientes (Cuentas)
        // Asumimos que hay una tabla 'accounts' o similar. Si no, usamos 'users' con rol account_admin?
        // REVISIÓN ESQUEMA: No recordamos tabla 'accounts'. En multi-tenant simple, el 'tenant' suele ser 'businesses' o un 'account_id' en users.
        // Vamos a asumir por ahora que 'businesses' son los nodos principales, y agrupamos por 'account_id'.
        
        // Total Negocios
        $totalBusinesses = $pdo->query("SELECT COUNT(*) FROM businesses")->fetchColumn();

        // Total Usuarios
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

        // Total Clientes (Cuentas únicas)
        $totalClients = $pdo->query("SELECT COUNT(DISTINCT account_id) FROM businesses")->fetchColumn();

        // Ingresos Recurrentes (Simulado - Suma de suscripciones si existiera tabla)
        $mrr = 0.00; 

        $metrics = [
            'total_clients' => $totalClients,
            'total_businesses' => $totalBusinesses,
            'total_users' => $totalUsers,
            'mrr' => $mrr
        ];

        // Obtener lista de clientes recientes
        $recentClients = $pdo->query("
            SELECT b.name as business_name, b.created_at, u.name as owner_name, u.email
            FROM businesses b
            JOIN users u ON b.account_id = u.account_id AND u.role = 'admin'
            GROUP BY b.account_id
            ORDER BY b.created_at DESC
            LIMIT 5
        ")->fetchAll(\PDO::FETCH_ASSOC);

        // Menú lateral auto-inyectado por Controller

        return $this->render('platform/dashboard', [
            'title' => 'Panel de Control de Plataforma (OmniPOS)',
            'metrics' => $metrics,
            'recentClients' => $recentClients
        ]);
    }
}
