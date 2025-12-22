<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use OmniPOS\Services\TenantService;

class TaxController extends Controller
{
    public function index(\OmniPOS\Core\Request $request, \OmniPOS\Core\Response $response)
    {
        $this->checkPermission('manage_settings');
        $this->view->setLayout('admin');
        
        $pdo = Database::connect();
        $businessId = Session::get('business_id');

        $stmt = $pdo->prepare("SELECT * FROM tax_rates WHERE business_id = :bid ORDER BY name ASC");
        $stmt->execute(['bid' => $businessId]);
        $rates = $stmt->fetchAll();

        // Get IGTF from config
        $igtf = \OmniPOS\Services\ConfigService::getInstance()->get('igtf_percentage', 3.0);

        return $this->render('admin/tax_rates', [
            'title' => 'Configuración de Impuestos',
            'rates' => $rates,
            'igtf' => $igtf
        ]);
    }

    public function store(\OmniPOS\Core\Request $request, \OmniPOS\Core\Response $response)
    {
        $this->checkPermission('manage_settings');
        $pdo = Database::connect();
        $businessId = Session::get('business_id');
        $data = $request->all();

        $sql = "INSERT INTO tax_rates (id, business_id, name, percentage, is_default) 
                VALUES (UUID(), :bid, :name, :pct, :def)";
        
        $pdo->prepare($sql)->execute([
            'bid' => $businessId,
            'name' => $data['name'],
            'pct' => $data['percentage'],
            'def' => isset($data['is_default']) ? 1 : 0
        ]);

        if (isset($data['is_default'])) {
            // Reset other defaults
            $pdo->prepare("UPDATE tax_rates SET is_default = 0 WHERE business_id = :bid AND name != :name")
                ->execute(['bid' => $businessId, 'name' => $data['name']]);
        }

        $response->redirect('/admin/taxes');
    }

    public function updateIgtf(\OmniPOS\Core\Request $request, \OmniPOS\Core\Response $response)
    {
        $this->checkPermission('manage_settings');
        $pdo = Database::connect();
        $businessId = Session::get('business_id');
        $percentage = $request->get('igtf_percentage');

        $stmt = $pdo->prepare("SELECT id FROM global_config WHERE business_id = :bid AND config_key = 'igtf_percentage'");
        $stmt->execute(['bid' => $businessId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $pdo->prepare("UPDATE global_config SET config_value = :val WHERE id = :id")
                ->execute(['val' => $percentage, 'id' => $existing['id']]);
        } else {
            $pdo->prepare("INSERT INTO global_config (id, business_id, config_key, config_value, value_type) VALUES (UUID(), :bid, 'igtf_percentage', :val, 'number')")
                ->execute(['bid' => $businessId, 'val' => $percentage]);
        }

        $response->redirect('/admin/taxes');
    }

    public function delete(\OmniPOS\Core\Request $request, \OmniPOS\Core\Response $response)
    {
        $this->checkPermission('manage_settings');
        $id = $request->get('id');
        $pdo = Database::connect();
        $businessId = Session::get('business_id');

        $pdo->prepare("DELETE FROM tax_rates WHERE id = :id AND business_id = :bid")
            ->execute(['id' => $id, 'bid' => $businessId]);

        $response->redirect('/admin/taxes');
    }
}
