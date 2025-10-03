<?php

namespace Celovel\View;

class ViewService
{
    protected string $viewsPath;
    protected string $cachePath;
    protected BladeCompiler $compiler;

    public function __construct(?string $viewsPath = null, ?string $cachePath = null)
    {
        $this->viewsPath = $viewsPath ?: __DIR__ . '/../../resources/views';
        $this->cachePath = $cachePath ?: __DIR__ . '/../../storage/framework/views';
        $this->compiler = new BladeCompiler($this->viewsPath, $this->cachePath);
    }

    public function make(string $view, array $data = []): BladeView
    {
        return BladeView::make($this->compiler, $view, $data);
    }

    public function exists(string $view): bool
    {
        $viewPath = str_replace('.', '/', $view);
        $filePath = $this->viewsPath . '/' . $viewPath . '.blade.php';
        
        return file_exists($filePath);
    }

    public function render(string $view, array $data = []): string
    {
        return $this->make($view, $data)->render();
    }

    public function getViewsPath(): string
    {
        return $this->viewsPath;
    }

    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    public function clearCache(): void
    {
        $this->compiler->clearCache();
    }

    public function getCompiler(): BladeCompiler
    {
        return $this->compiler;
    }
}
