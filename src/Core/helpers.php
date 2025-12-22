<?php

if (!function_exists('__')) {
    /**
     * Alias global para la función de traducción.
     * 
     * @param string $key Clave de traducción.
     * @param array $params Parámetros para reemplazo (:key).
     * @return string
     */
    function __(string $key, array $params = []): string
    {
        return \OmniPOS\Services\I18nService::translate($key, $params);
    }
}

if (!function_exists('url')) {
    /**
     * Genera una URL completa basada en la configuración de la APP.
     * 
     * @param string $path Ruta relativa.
     * @return string
     */
    function url(string $path = ''): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? getenv('APP_URL');
        $baseUrl = $baseUrl ? rtrim($baseUrl, '/') : '';
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }
}
if (!function_exists('formatRole')) {
    /**
     * Formatea el nombre técnico del rol para mostrarlo al usuario.
     * 
     * @param string $role Nombre técnico (ej: super_admin).
     * @return string
     */
    function formatRole(string $role): string
    {
        return ucwords(str_replace('_', ' ', $role));
    }
}
