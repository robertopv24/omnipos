<?php

namespace OmniPOS\Core;

class MiddlewareManager
{
    protected array $stack = [];

    public function add(string $middleware): void
    {
        $this->stack[] = $middleware;
    }

    public function run(Request $request, Response $response, callable $core): void
    {
        // This is a simplified middleware runner
        // A real one typically uses a pipeline/onion pattern
        // For MVP, we might just loop or simple nesting

        // TODO: Implement proper pipeline
    }
}
