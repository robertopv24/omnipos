<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use OmniPOS\Services\MenuService;

class AuditController extends Controller
{
    public function authorizations(Request $request, Response $response)
    {
        $this->checkPermission('view_reports');
        $this->view->setLayout('admin');

        $pdo = Database::connect();
        $businessId = Session::get('business_id');

        // Obtener historial de autorizaciones
        $sql = "SELECT a.*, 
                       u.name as cashier_name, 
                       s.name as supervisor_name,
                       o.total_price as amount
                FROM authorization_logs a
                LEFT JOIN users u ON a.user_id = u.id
                LEFT JOIN users s ON a.supervisor_id = s.id
                LEFT JOIN orders o ON a.reference_id = o.id
                WHERE a.business_id = :bid
                ORDER BY a.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['bid' => $businessId]);
        $logs = $stmt->fetchAll();

        return $this->render('admin/audit_authorizations', [
            'title' => 'Auditoría de Autorizaciones',
            'logs' => $logs
        ]);
    }
}
