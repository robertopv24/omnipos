<?php

namespace OmniPOS\Core;

class App
{
    protected Router $router;
    protected Request $request;
    protected Response $response;
    protected MiddlewareManager $middlewareManager;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->middlewareManager = new MiddlewareManager();
        $this->router = new Router($this->request, $this->response);
    }

    public function run(): void
    {
        // 1. Resolve Route
        // 2. Run Global Middleware
        // 3. Run Route Middleware
        // 4. Execute Controller/Callback

        $this->router->resolve();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
