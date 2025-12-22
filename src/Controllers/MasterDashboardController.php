<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Services\AccountingService;
use OmniPOS\Services\MenuService;

class MasterDashboardController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct()
    {
        parent::__construct();
        $this->accountingService = new AccountingService();
    }

    /**
     * Vista principal del Dashboard Consolidados.
     */
    public function index(Request $request, Response $response)
    {
        $role = Session::get('role');

        // Solo admins y super admins pueden ver esto
        if (!in_array($role, ['admin', 'account_admin', 'super_admin'])) {
            $response->redirect('/dashboard');
            return;
        }

        $this->view->setLayout('admin');

        $activeAccountId = Session::get('impersonated_account_id') ?? Session::get('account_id');
        $metrics = $this->accountingService->getConsolidatedMetrics($activeAccountId);

        return $this->render('dashboard/master', [
            'title' => 'Tablero Maestro (Consolidado)',
            'metrics' => $metrics
        ]);
    }
}
