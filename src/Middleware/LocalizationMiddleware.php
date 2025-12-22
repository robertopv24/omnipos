<?php

namespace OmniPOS\Middleware;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;
use OmniPOS\Core\Database;
use OmniPOS\Services\I18nService;
use OmniPOS\Services\LocalizationService;
use PDO;

class LocalizationMiddleware
{
    public function handle(Request $request, Response $response): bool
    {
        $businessId = Session::get('business_id');

        if ($businessId) {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("SELECT language, timezone, country, theme, theme_settings, logo_path, client_logo_path FROM businesses WHERE id = :id");
            $stmt->execute(['id' => $businessId]);
            $config = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($config) {
                // Set language
                I18nService::setLocale($config['language'] ?: 'es');

                // Set Timezone
                if ($config['timezone']) {
                    date_default_timezone_set($config['timezone']);
                }

                // Set Country for formats
                LocalizationService::setCountry($config['country'] ?: 'VE');

                // Set theme in session for layout
                Session::set('app_theme', $config['theme'] ?: 'default');
                Session::set('theme_settings', json_decode($config['theme_settings'], true) ?: []);
                Session::set('business_logo', $config['logo_path']);
                Session::set('client_logo', $config['client_logo_path']);
            }
        } else {
            // Defaults
            I18nService::setLocale('es');
            LocalizationService::setCountry('VE');
            Session::set('app_theme', 'default');
        }

        return true;
    }
}
