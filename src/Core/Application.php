<?php

namespace Celovel\Core;

use Celovel\Http\Router;
use Celovel\Http\Request;
use Celovel\Http\Response;
use Celovel\Support\ServiceContainer;
use Celovel\Support\Config;
use Celovel\Support\Env;
use Celovel\Database\Connection;
use Celovel\View\ViewService;

class Application
{
    protected ServiceContainer $container;
    protected Router $router;
    protected string $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?: dirname(__DIR__, 2);
        $this->container = new ServiceContainer();
        $this->router = new Router();
        
        $this->registerServices();
    }

    protected function registerServices(): void
    {
        // Load environment variables
        Env::load();
        
        // Register core services
        $this->container->singleton('app', $this);
        $this->container->singleton('router', $this->router);
        $this->container->singleton('config', function() {
            return new Config();
        });
        
        // Register database connection
        $this->container->singleton(Connection::class, function() {
            $config = $this->container->make('config');
            $dbConfig = $config->get('database.connections.' . $config->get('database.default'));
            return new Connection($dbConfig);
        });
        
        // Register view service
        $this->container->singleton(ViewService::class, function() {
            return new ViewService();
        });
        
        // Register view service with alias
        $this->container->singleton('view', function() {
            return $this->container->make(ViewService::class);
        });
        
        // Register middleware
        $this->registerMiddleware();
    }

    protected function registerMiddleware(): void
    {
        // Global middleware'leri kaydet
        $this->router->registerGlobalMiddleware(\App\Http\Middleware\LoggingMiddleware::class);
        $this->router->registerGlobalMiddleware(\App\Http\Middleware\CorsMiddleware::class);
        
        // Named middleware'leri kaydet
        $this->router->registerMiddleware('auth', \App\Http\Middleware\AuthMiddleware::class);
        $this->router->registerMiddleware('cors', \App\Http\Middleware\CorsMiddleware::class);
        $this->router->registerMiddleware('logging', \App\Http\Middleware\LoggingMiddleware::class);
    }

    public function run(): void
    {
        $this->loadRoutes();
        
        $request = Request::createFromGlobals();
        $response = $this->router->dispatch($request);
        $response->send();
    }

    // Router method'larını proxy et
    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->router->get($path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->router->post($path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->router->put($path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->router->delete($path, $handler, $middleware);
    }

    public function loadRoutes(): void
    {
        $routesPath = $this->basePath . '/routes/web.php';
        
        if (file_exists($routesPath)) {
            $app = $this;
            require $routesPath;
        }
    }


    public function getContainer(): ServiceContainer
    {
        return $this->container;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
