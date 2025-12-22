<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;

use OmniPOS\Services\MenuService;

class DashboardController extends Controller
{
    public function index(Request $request, Response $response)
    {
        // El dashboard es la vista principal, permitimos ver métricas básicas
        $this->checkPermission('view_metrics');
        $this->view->setLayout('admin');

        $accountingService = new \OmniPOS\Services\AccountingService();
        $accountId = Session::get('account_id');
        $metrics = $accountingService->getConsolidatedMetrics($accountId);

        return $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user_name' => Session::get('user_name'),
            'metrics' => $metrics
        ]);
    }
}
