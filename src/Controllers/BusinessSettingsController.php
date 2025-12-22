<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;
use OmniPOS\Services\MenuService;
use PDO;

class BusinessSettingsController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $this->view->setLayout('admin');
        $businessId = Session::get('business_id');

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = :id");
        $stmt->execute(['id' => $businessId]);
        $business = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($business && $business['theme_settings']) {
            $business['theme_settings'] = json_decode($business['theme_settings'], true);
        } else {
            $business['theme_settings'] = ['primary' => '#3b82f6', 'secondary' => '#1e293b'];
        }

        return $this->render('account/settings', [
            'title' => __('settings'),
            'business' => $business
        ]);
    }

    public function update(Request $request, Response $response)
    {
        $this->checkPermission('manage_settings');
        $businessId = Session::get('business_id');
        $data = $request->all();

        $pdo = Database::connect();

        // Obtener datos actuales para posibles borrados de archivos antiguos
        $stmt = $pdo->prepare("SELECT logo_path, client_logo_path FROM businesses WHERE id = :id");
        $stmt->execute(['id' => $businessId]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        $uploadService = new \OmniPOS\Services\UploadService('logos');

        $logoPath = $current['logo_path'];
        $clientLogoPath = $current['client_logo_path'];

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $newLogo = $uploadService->upload($_FILES['logo']);
            if ($newLogo) {
                if ($logoPath)
                    $uploadService->delete($logoPath);
                $logoPath = 'logos/' . $newLogo;
            }
        }

        if (isset($_FILES['client_logo']) && $_FILES['client_logo']['error'] === UPLOAD_ERR_OK) {
            $newClientLogo = $uploadService->upload($_FILES['client_logo']);
            if ($newClientLogo) {
                if ($clientLogoPath)
                    $uploadService->delete($clientLogoPath);
                $clientLogoPath = 'logos/' . $newClientLogo;
            }
        }

        $themeSettings = json_encode([
            'primary' => $data['primary_color'] ?? '#3b82f6',
            'secondary' => $data['secondary_color'] ?? '#1e293b'
        ]);

        $sql = "UPDATE businesses SET 
                country = :country, 
                language = :language, 
                timezone = :timezone, 
                theme = :theme, 
                currency = :currency,
                tax_id = :tax_id,
                theme_settings = :theme_settings,
                logo_path = :logo_path,
                client_logo_path = :client_logo_path
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            'country' => $data['country'],
            'language' => $data['language'],
            'timezone' => $data['timezone'],
            'theme' => $data['theme'],
            'currency' => $data['currency'],
            'tax_id' => $data['tax_id'],
            'theme_settings' => $themeSettings,
            'logo_path' => $logoPath,
            'client_logo_path' => $clientLogoPath,
            'id' => $businessId
        ]);

        if ($success) {
            $response->redirect('/account/settings?success=1');
        } else {
            $response->redirect('/account/settings?error=1');
        }
    }
}
