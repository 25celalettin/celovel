<?php

namespace Celovel\Console\Commands;

use Celovel\Console\Command;

class RouteListCommand extends Command
{
    public function execute(array $arguments, array $options): void
    {
        $app = new \Celovel\Core\Application();
        $app->loadRoutes(); // Route'ları yükle
        $router = $app->getContainer()->make('router');
        $routes = $router->getRoutes();
        
        if (empty($routes)) {
            $this->warning('No routes found.');
            return;
        }

        $this->info('Registered Routes:');
        $this->line('');
        
        // Header
        $this->line(sprintf('%-8s %-30s %-50s %-20s', 'Method', 'URI', 'Action', 'Middleware'));
        $this->line(str_repeat('-', 108));
        
        foreach ($routes as $route) {
            $method = $route['method'];
            $uri = $route['path'];
            $action = $this->formatAction($route['handler']);
            $middleware = $this->formatMiddleware($route['middleware'] ?? []);
            
            $this->line(sprintf('%-8s %-30s %-50s %-20s', $method, $uri, $action, $middleware));
        }
    }

    protected function formatAction($handler): string
    {
        if (is_string($handler) && str_contains($handler, '@')) {
            return $handler;
        }
        
        if (is_array($handler) && count($handler) === 2) {
            return $handler[0] . '@' . $handler[1];
        }
        
        if (is_callable($handler)) {
            return 'Closure';
        }
        
        return 'Unknown';
    }

    protected function formatMiddleware(array $middleware): string
    {
        if (empty($middleware)) {
            return 'None';
        }
        
        return implode(', ', $middleware);
    }

    public function getDescription(): string
    {
        return 'List all registered routes';
    }
}
