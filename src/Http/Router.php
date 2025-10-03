<?php

namespace Celovel\Http;

use Celovel\Http\Request;
use Celovel\Http\Response;
use Celovel\Http\Middleware\MiddlewareManager;

class Router
{
    protected array $routes = [];
    protected array $middleware = [];
    protected MiddlewareManager $middlewareManager;

    public function __construct()
    {
        $this->middlewareManager = new MiddlewareManager();
    }

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    protected function addRoute(string $method, string $path, $handler, array $middleware = []): void
    {
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                return $this->handleRoute($route, $request);
            }
        }

        return new Response('404 Not Found', 404);
    }

    protected function matchPath(string $routePath, string $requestPath): bool
    {
        // Basit path matching - daha sonra parametre desteği ekleyeceğiz
        if ($routePath === $requestPath) {
            return true;
        }

        // Parametre desteği için regex matching
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }

    protected function handleRoute(array $route, Request $request): Response
    {
        $handler = $route['handler'];
        $middleware = $route['middleware'] ?? [];
        

        // Handler'ı closure'a çevir
        $handlerClosure = function (Request $request) use ($handler) {
            if (is_string($handler) && str_contains($handler, '@')) {
                [$controller, $method] = explode('@', $handler);
                $controllerInstance = new $controller($request);
                $result = $controllerInstance->$method($request);
                
                // Eğer array döndürülmüşse JSON response'a çevir
                if (is_array($result)) {
                    return (new Response())->json($result);
                }
                
                return $result;
            }

            if (is_array($handler) && count($handler) === 2) {
                [$controller, $method] = $handler;
                $controllerInstance = new $controller($request);
                $result = $controllerInstance->$method($request);
                
                // Eğer array döndürülmüşse JSON response'a çevir
                if (is_array($result)) {
                    return (new Response())->json($result);
                }
                
                return $result;
            }

            if (is_callable($handler)) {
                $result = $handler($request);
                
                // Eğer array döndürülmüşse JSON response'a çevir
                if (is_array($result)) {
                    return (new Response())->json($result);
                }
                
                return $result;
            }

            return new Response('Invalid handler', 500);
        };

        // Middleware'leri çalıştır
        return $this->middlewareManager->run($middleware, $request, $handlerClosure);
    }

    public function middleware(string $name, callable $middleware): void
    {
        $this->middleware[$name] = $middleware;
    }

    public function registerMiddleware(string $name, $middleware): void
    {
        $this->middlewareManager->register($name, $middleware);
    }

    public function registerGlobalMiddleware($middleware): void
    {
        $this->middlewareManager->registerGlobal($middleware);
    }

    public function getMiddlewareManager(): MiddlewareManager
    {
        return $this->middlewareManager;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
