<?php

namespace OmniPOS\Widgets;

use OmniPOS\Core\ViewRenderer;

abstract class BaseWidget
{
    protected ViewRenderer $view;

    public function __construct()
    {
        $this->view = new ViewRenderer();
    }

    /**
     * Render the widget HTML
     * @param array $props Properties passed from the builder
     * @return string
     */
    abstract public function render(array $props = []): string;
}
