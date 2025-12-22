<?php

namespace OmniPOS\Services;

class PageRendererService
{
    public function render(array $layoutData): string
    {
        if (!isset($layoutData['nodes']) || !is_array($layoutData['nodes'])) {
            return '<div class="alert alert-warning">Página vacía o inválida.</div>';
        }

        $html = '';
        foreach ($layoutData['nodes'] as $node) {
            $html .= $this->renderNode($node);
        }
        return $html;
    }

    private function renderNode(array $node): string
    {
        $type = $node['type'] ?? 'div';
        $props = $node['props'] ?? [];
        $children = $node['children'] ?? [];
        
        $tag = $props['tagName'] ?? 'div';
        $attributes = $props['attributes'] ?? [];
        $styles = $this->renderStyles($props);
        $customClass = $props['customClass'] ?? '';
        $text = $props['text'] ?? '';

        // Handle Widgets
        if ($type === 'widget') {
            $widgetName = $props['name'] ?? ''; 
            $className = "OmniPOS\\Widgets\\" . $widgetName;
            if (class_exists($className)) {
                $widget = new $className();
                return '<div class="' . $customClass . '" ' . $styles . '>' . $widget->render($props) . '</div>';
            }
            return '<div class="alert alert-danger">Widget not found: ' . htmlspecialchars($widgetName) . '</div>';
        }

        // Handle Specialized Structures (e.g. Column grid conversion)
        if ($tag === 'div' && isset($attributes['width'])) {
            $customClass .= " col-md-" . $attributes['width'];
            unset($attributes['width']);
        }

        // Build Attributes String
        $attrStr = '';
        foreach ($attributes as $key => $val) {
            $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($val) . '"';
        }

        // Self-closing tags
        $selfClosing = ['img', 'hr', 'br', 'input', 'meta', 'link'];
        if (in_array($tag, $selfClosing)) {
            return "<$tag class=\"$customClass\" $styles $attrStr />";
        }

        // Container / Standard Tag
        $innerHtml = $this->resolveText($text) . $this->renderChildren($children);
        
        return "<$tag class=\"$customClass\" $styles $attrStr>$innerHtml</$tag>";
    }

    private function renderStyles(array $props): string
    {
        $styleData = $props['styles'] ?? [];
        if (empty($styleData)) return '';
        
        $css = [];
        foreach ($styleData as $key => $value) {
            if ($value) $css[] = "$key: $value";
        }
        return !empty($css) ? ' style="' . implode('; ', $css) . '"' : '';
    }

    private function renderChildren(array $children): string
    {
        $html = '';
        foreach ($children as $child) {
            $html .= $this->renderNode($child);
        }
        return $html;
    }

    private function resolveText(string $text): string
    {
        if (empty($text)) return '';
        // i18n support
        if (strpos($text, 'i18n:') === 0) {
            return __(substr($text, 5));
        }
        // Allow raw HTML for advanced layouts (admin-only tool)
        return $text;
    }
}
