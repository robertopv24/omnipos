<?php

namespace OmniPOS\Core;

class Router
{
    protected array $routes = [];
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback, array $middleware = []): void
    {
        $this->routes['get'][$path] = ['callback' => $callback, 'middleware' => $middleware];
    }

    public function post(string $path, $callback, array $middleware = []): void
    {
        $this->routes['post'][$path] = ['callback' => $callback, 'middleware' => $middleware];
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = strtolower($this->request->getMethod());
        
        // 1. Try Exact Match
        $route = $this->routes[$method][$path] ?? false;

        if ($route) {
            $this->executeRoute($route, []);
            return;
        }

        // 2. Try Regex Match (Dynamic Params)
        foreach ($this->routes[$method] as $routePath => $routeConfig) {
            // Optimization: Skip if no parameters
            if (strpos($routePath, '{') === false) {
                continue;
            }

            // Convert {param} to capture group. 
            // We use (.+) to allow multi-segment slugs for the wildcard
            $pattern = "#^" . preg_replace('/\{[a-zA-Z0-9_]+\}/', '(.+)', $routePath) . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); 
                // Normalize params: if they have leading/trailing slashes, trim them for cleaner controller logic
                $params = array_map(function($m) { return trim($m, '/'); }, $matches);
                $this->executeRoute($routeConfig, $params);
                return;
            }
        }

        // 3. Not Found
        $this->response->setStatusCode(404);
        echo "404 Not Found";
    }

    protected function executeRoute(array $route, array $params = [])
    {
        $callback = $route['callback'];
        $middlewares = $route['middleware'];

        // Run Middlewares
        foreach ($middlewares as $mw) {
            if (is_string($mw)) {
                $mwClass = "OmniPOS\\Middleware\\" . $mw;
                if (class_exists($mwClass)) {
                    $mwInstance = new $mwClass();
                    if (!$mwInstance->handle($this->request, $this->response)) {
                        return; // Middleware intercepted response
                    }
                }
            }
        }

        // Execute Callback
        if (is_string($callback)) {
            $parts = explode('@', $callback);
            $controllerName = "OmniPOS\\Controllers\\" . $parts[0];
            $action = $parts[1];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                // Merge Request/Response with URL Params
                $args = array_merge([$this->request, $this->response], $params);
                echo call_user_func_array([$controller, $action], $args);
            } else {
                echo "Controller not found: $controllerName";
            }
            return;
        }

        // Closure callback
        $args = array_merge([$this->request, $this->response], $params);
        echo call_user_func_array($callback, $args);
    }
}
