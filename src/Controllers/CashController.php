<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Services\CashService;
use OmniPOS\Services\MenuService;

class CashController extends Controller
{
    protected CashService $cashService;

    public function __construct()
    {
        parent::__construct();
        $this->cashService = new CashService();
    }

    /**
     * Muestra el estado actual de la caja o reporte de sesiones.
     */
    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_cash');
        $this->view->setLayout('admin');
        $session = $this->cashService->getOpenSession(Session::get('user_id'));

        return $this->render('cash/index', [
            'title' => 'Gestión de Caja',
            'session' => $session
        ]);
    }

    /**
     * Inicia una nueva sesión de caja.
     */
    public function open(Request $request, Response $response)
    {
        $this->checkPermission('manage_cash');
        if ($request->isPost()) {
            $data = $request->all();
            if ($this->cashService->openSession($data)) {
                $response->redirect('/pos');
            }
        }

        $this->view->setLayout('admin');
        return $this->render('cash/open', [
            'title' => 'Apertura de Caja'
        ]);
    }

    /**
     * Cierra la sesión de caja actual.
     */
    public function close(Request $request, Response $response)
    {
        $this->checkPermission('manage_cash');
        $session = $this->cashService->getOpenSession(Session::get('user_id'));
        if (!$session)
            $response->redirect('/cash');

        if ($request->isPost()) {
            if ($this->cashService->closeSession($session['id'], $request->all())) {
                $response->redirect('/cash');
            }
        }

        $balances = $this->cashService->getCalculatedBalances($session['id']);

        $this->view->setLayout('admin');
        return $this->render('cash/close', [
            'title' => 'Cierre de Caja',
            'session' => $session,
            'balances' => $balances
        ]);
    }

    /**
     * Registra un movimiento manual (Caja Chica).
     */
    public function movement(Request $request, Response $response)
    {
        $this->checkPermission('manage_cash');
        if ($request->isPost()) {
            if ($this->cashService->registerMovement($request->all())) {
                $response->redirect('/cash');
            }
        }

        // Obtener métodos de pago
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE business_id = :bid AND is_active = 1");
        $stmt->execute(['bid' => Session::get('business_id')]);
        $paymentMethods = $stmt->fetchAll();

        $this->view->setLayout('admin');
        return $this->render('cash/movement', [
            'title' => 'Movimiento de Caja Chica',
            'paymentMethods' => $paymentMethods
        ]);
    }
}
