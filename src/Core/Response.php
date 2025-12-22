<?php

namespace OmniPOS\Core;

class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $url): void
    {
        if (str_starts_with($url, '/')) {
            $url = url($url);
        }
        header("Location: $url");
        exit;
    }

    public function json(array $data, int $status = 200): void
    {
        $this->setStatusCode($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
