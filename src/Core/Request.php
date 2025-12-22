<?php

namespace OmniPOS\Core;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function getPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = strtok($uri, '?');

        // Obtener la ruta del script actual (ej: /omnipos/public/index.php)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        // Obtener el directorio base (ej: /omnipos/public)
        $basePath = str_replace('\\', '/', dirname($scriptName));

        // Si el basePath no es la raíz y el path comienza con él, lo removemos
        if ($basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        } 
        // Caso especial: Si redireccionamos desde la raíz del subdirectorio (ej: /omnipos/)
        // el basePath detectado podría ser /omnipos/public pero la URI es /omnipos/
        else {
            $parentBase = str_replace('\\', '/', dirname($basePath));
            if ($parentBase !== '/' && strpos($path, $parentBase) === 0) {
                $path = substr($path, strlen($parentBase));
            }
        }

        return '/' . ltrim($path, '/');
    }

    public function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $_REQUEST[$key] ?? $default;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->input($key, $default);
    }

    public function all(): array
    {
        // Simple merge of GET and POST and JSON body if needed
        return array_merge($_GET, $_POST);
    }

    /**
     * Get JSON body
     */
    public function getBody(): array
    {
        if ($this->isPost() && empty($_POST)) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            return is_array($data) ? $data : [];
        }

        return $_POST;
    }
}
