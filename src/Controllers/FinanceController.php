<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Services\FinanceService;
use OmniPOS\Services\MenuService;
use OmniPOS\Services\CashService;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct()
    {
        parent::__construct();
        $this->financeService = new FinanceService();
    }

    /**
     * Listado de Cuentas por Cobrar (CXC).
     */
    public function cxc(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $this->view->setLayout('admin');

        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        $pending = $this->financeService->getPendingCxc($businessId);

        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT id, name FROM payment_methods WHERE business_id = :bid AND is_active = 1");
        $stmt->execute(['bid' => $businessId]);
        $paymentMethods = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('finance/cxc', [
            'title' => 'Cuentas por Cobrar',
            'pendingCxc' => $pending,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Procesa un abono/pago a una CXC.
     */
    public function payCxc(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $id = $request->get('id');
        $data = $request->all();

        // Validar monto
        $validator = new \OmniPOS\Core\Validator($data, [
            'amount' => 'required|numeric|positive'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/finance/cxc');
        }

        try {
            // Obtener sesión abierta para vincular el ingreso a caja
            $cashService = new CashService();
            $businessId = \OmniPOS\Services\TenantService::getBusinessId();
            $cxc = $this->financeService->getCxcById($id);

            if (!$cxc || $cxc['business_id'] !== $businessId) {
                Session::flash('error', 'Cuenta por cobrar no encontrada.');
                return $response->redirect('/finance/cxc');
            }

            if ($this->financeService->recordCxcPayment($id, $data)) {
                Session::flash('success', 'Pago registrado exitosamente');
                $response->redirect('/finance/cxc');
            } else {
                Session::flash('error', 'Error al procesar el pago');
                $response->redirect('/finance/cxc');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            $response->redirect('/finance/cxc');
        }
    }

    /**
     * Listado de Cuentas por Pagar (CXP).
     */
    public function cxp(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $this->view->setLayout('admin');

        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        $pending = $this->financeService->getPendingCxp($businessId);

        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT id, name FROM payment_methods WHERE business_id = :bid AND is_active = 1");
        $stmt->execute(['bid' => $businessId]);
        $paymentMethods = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('finance/cxp', [
            'title' => 'Cuentas por Pagar',
            'pendingCxp' => $pending,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Procesa un pago a una CXP.
     */
    public function payCxp(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $id = $request->get('id');
        $data = $request->all();

        // Validar monto
        $validator = new \OmniPOS\Core\Validator($data, [
            'amount' => 'required|numeric|positive'
        ]);

        if (!$validator->validate()) {
            Session::flash('error', $validator->firstError());
            return $response->redirect('/finance/cxp');
        }

        try {
            $cashService = new CashService();
            $businessId = \OmniPOS\Services\TenantService::getBusinessId();
            $cxp = $this->financeService->getCxpById($id);

            if (!$cxp || $cxp['business_id'] !== $businessId) {
                Session::flash('error', 'Cuenta por pagar no encontrada.');
                return $response->redirect('/finance/cxp');
            }

            if ($this->financeService->recordCxpPayment($id, $data)) {
                Session::flash('success', 'Pago a proveedor registrado exitosamente');
                $response->redirect('/finance/cxp');
            } else {
                Session::flash('error', 'Error al procesar el pago');
                $response->redirect('/finance/cxp');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            $response->redirect('/finance/cxp');
        }
    }

    /**
     * Listado y gestión de Nómina.
     */
    public function payroll(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $this->view->setLayout('admin');

        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        $payrollService = new \OmniPOS\Services\PayrollService();
        $payments = $payrollService->getPayments($businessId);

        // Obtener empleados (usuarios)
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE business_id = :bid");
        $stmt->execute(['bid' => $businessId]);
        $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Obtener métodos de pago
        $stmt = $pdo->prepare("SELECT id, name FROM payment_methods WHERE business_id = :bid AND is_active = 1");
        $stmt->execute(['bid' => $businessId]);
        $paymentMethods = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('finance/payroll', [
            'title' => 'Gestión de Nómina',
            'payments' => $payments,
            'employees' => $employees,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Procesa un pago de nómina.
     */
    public function payPayroll(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $payrollService = new \OmniPOS\Services\PayrollService();
        $data = $request->all();

        $cashService = new CashService();
        $session = $cashService->getOpenSession(Session::get('user_id'));

        if ($session) {
            $data['cash_session_id'] = $session['id'];
        }

        if ($payrollService->recordPayment($data)) {
            $response->redirect('/finance/payroll');
        } else {
            $response->redirect('/finance/payroll?error=failed');
        }
    }

    /**
     * Libro Contable (Diario).
     */
    public function ledger(Request $request, Response $response)
    {
        $this->checkPermission('view_finance');
        $this->view->setLayout('admin');

        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        $pdo = \OmniPOS\Core\Database::connect();
        
        $sql = "SELECT * FROM accounting_ledger WHERE business_id = :bid ORDER BY entry_date DESC, created_at DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        $entries = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('finance/ledger', [
            'title' => 'Libro Contable',
            'entries' => $entries
        ]);
    }

    /**
     * Gestión de Caja Chica.
     */
    public function pettyCash(Request $request, Response $response)
    {
        $this->checkPermission('manage_cash');
        $this->view->setLayout('admin');

        $businessId = \OmniPOS\Services\TenantService::getBusinessId();
        $pdo = \OmniPOS\Core\Database::connect();

        // Obtener transacciones de tipo 'expense' que no sean de órdenes o nómina
        $sql = "SELECT t.*, pm.name as method_name 
                FROM transactions t
                JOIN payment_methods pm ON t.payment_method_id = pm.id
                WHERE t.business_id = :bid 
                AND t.type = 'expense' 
                AND t.reference_type NOT IN ('order', 'adjustment')
                ORDER BY t.created_at DESC LIMIT 50";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        $movements = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Métodos de pago activos para el formulario
        $stmt = $pdo->prepare("SELECT id, name FROM payment_methods WHERE business_id = :bid AND is_active = 1");
        $stmt->execute(['bid' => $businessId]);
        $paymentMethods = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('finance/petty_cash', [
            'title' => 'Caja Chica',
            'movements' => $movements,
            'paymentMethods' => $paymentMethods
        ]);
    }
}
