<?php

namespace Celovel\View;

class View
{
    protected string $viewPath;
    protected array $data;
    protected string $layout;

    public function __construct(string $viewPath, array $data = [])
    {
        $this->viewPath = $viewPath;
        $this->data = $data;
    }

    public static function make(string $view, array $data = []): self
    {
        return new self($view, $data);
    }

    public function render(): string
    {
        $content = $this->renderView();
        
        if ($this->layout) {
            $content = $this->renderLayout($content);
        }

        return $content;
    }

    protected function renderView(): string
    {
        $filePath = $this->getViewFilePath();
        
        if (!file_exists($filePath)) {
            throw new \Exception("View [{$this->viewPath}] not found.");
        }

        extract($this->data);
        
        ob_start();
        include $filePath;
        return ob_get_clean();
    }

    protected function renderLayout(string $content): string
    {
        $layoutPath = $this->getLayoutFilePath();
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout [{$this->layout}] not found.");
        }

        extract($this->data);
        
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }

    protected function getViewFilePath(): string
    {
        $viewPath = str_replace('.', '/', $this->viewPath);
        return __DIR__ . '/../../app/Views/' . $viewPath . '.php';
    }

    protected function getLayoutFilePath(): string
    {
        return __DIR__ . '/../../app/Views/layouts/' . $this->layout . '.php';
    }

    public function with(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function layout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
