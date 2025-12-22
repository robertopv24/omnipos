<?php

namespace OmniPOS\Core;

class ViewRenderer
{
    protected string $viewPath;
    protected string $layout = 'main'; // default layout

    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../Views/';
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function render(string $view, array $data = []): string
    {
        extract($data);

        // Buffer the view content
        ob_start();
        $viewFile = $this->viewPath . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View not found: $view";
        }
        $content = ob_get_clean();

        // Render layout wrapping the content
        // Helper specifically for use inside layout
        $viewContent = $content;

        ob_start();
        $layoutFile = $this->viewPath . 'layouts/' . $this->layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            // Fallback if layout missing, just echo content
            echo $content;
        }

        return ob_get_clean();
    }
}
