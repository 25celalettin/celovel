<?php

namespace Celovel\Http\Middleware;

use Celovel\Http\Request;
use Celovel\Http\Response;

class MiddlewareManager
{
    protected array $middleware = [];
    protected array $globalMiddleware = [];

    public function register(string $name, $middleware): void
    {
        $this->middleware[$name] = $middleware;
    }

    public function registerGlobal($middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    public function run(array $middleware, Request $request, \Closure $next): Response
    {
        $middleware = array_merge($this->globalMiddleware, $middleware);
        
        
        if (empty($middleware)) {
            return $next($request);
        }

        $pipeline = $this->createPipeline($middleware, $next);
        return $pipeline($request);
    }

    protected function createPipeline(array $middleware, \Closure $next): \Closure
    {
        return array_reduce(
            array_reverse($middleware),
            function ($carry, $middleware) {
                return function ($request) use ($middleware, $carry) {
                    return $this->resolveMiddleware($middleware)->handle($request, $carry);
                };
            },
            $next
        );
    }

    protected function resolveMiddleware($middleware)
    {
        
        if (is_string($middleware)) {
            // Önce named middleware'leri kontrol et
            if (isset($this->middleware[$middleware])) {
                $middleware = $this->middleware[$middleware];
            }
            // Eğer class ismi ise direkt kullan
            elseif (class_exists($middleware)) {
                return new $middleware();
            } else {
                throw new \Exception("Middleware [{$middleware}] not registered.");
            }
        }

        if (is_string($middleware) && class_exists($middleware)) {
            return new $middleware();
        }

        if (is_callable($middleware)) {
            return new class($middleware) extends Middleware {
                protected $callable;

                public function __construct($callable)
                {
                    $this->callable = $callable;
                }

                public function handle(Request $request, \Closure $next): Response
                {
                    return ($this->callable)($request, $next);
                }
            };
        }

        if ($middleware instanceof Middleware) {
            return $middleware;
        }

        throw new \Exception("Invalid middleware type.");
    }

    public function getMiddleware(string $name)
    {
        return $this->middleware[$name] ?? null;
    }

    public function getGlobalMiddleware(): array
    {
        return $this->globalMiddleware;
    }

    public function getAllMiddleware(): array
    {
        return $this->middleware;
    }
}
