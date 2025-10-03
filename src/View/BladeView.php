<?php

namespace Celovel\View;

class BladeView
{
    protected BladeCompiler $compiler;
    protected string $viewPath;
    protected array $data;
    protected ?string $layout = null;
    protected array $sections = [];
    protected array $sectionStack = [];

    public function __construct(BladeCompiler $compiler, string $viewPath, array $data = [])
    {
        $this->compiler = $compiler;
        $this->viewPath = $viewPath;
        $this->data = $data;
    }

    public static function make(BladeCompiler $compiler, string $view, array $data = []): self
    {
        return new self($compiler, $view, $data);
    }

    public function render(): string
    {
        $compiledPath = $this->compiler->compile($this->viewPath);
        
        extract($this->data);
        
        ob_start();
        include $compiledPath;
        $content = ob_get_clean();
        
        if ($this->layout) {
            $content = $this->renderLayout($content);
        }

        return $content;
    }

    protected function renderLayout(string $content): string
    {
        $layoutPath = $this->compiler->compile($this->layout);
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout [{$this->layout}] not found.");
        }

        // Layout'u render etmeden önce content'i sections'a ekle
        $this->sections['content'] = $content;
        
        extract($this->data);
        
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }

    public function startSection(string $name): void
    {
        $this->sectionStack[] = $name;
        ob_start();
    }

    public function endSection(): void
    {
        if (empty($this->sectionStack)) {
            throw new \Exception('Cannot end a section without starting one.');
        }

        $name = array_pop($this->sectionStack);
        $this->sections[$name] = ob_get_clean();
    }

    public function yieldSection(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    public function includeView(string $view): string
    {
        $viewInstance = new self($this->compiler, $view, $this->data);
        return $viewInstance->render();
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

    // Helper methods for Blade directives
    public function __call(string $method, array $args)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$args);
        }
        
        throw new \Exception("Method [{$method}] not found.");
    }
}
