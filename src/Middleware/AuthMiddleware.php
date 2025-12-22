<?php

namespace OmniPOS\Middleware;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request, Response $response): bool
    {
        if (!Session::get('user_id')) {
            $response->redirect('/login');
            return false;
        }
        return true;
    }
}
