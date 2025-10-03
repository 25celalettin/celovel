<?php

namespace App\Http\Middleware;

use Celovel\Http\Middleware\Middleware;
use Celovel\Http\Request;
use Celovel\Http\Response;

class CorsMiddleware extends Middleware
{
    protected array $allowedOrigins = ['*'];
    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
    protected array $allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With'];

    public function handle(Request $request, \Closure $next): Response
    {
        // Preflight request'i handle et
        if ($request->getMethod() === 'OPTIONS') {
            return $this->handlePreflight($request);
        }

        $response = $next($request);

        // CORS header'larını ekle
        $this->addCorsHeaders($response, $request);

        return $response;
    }

    protected function handlePreflight(Request $request): Response
    {
        $response = new Response('', 200);
        $this->addCorsHeaders($response, $request);
        return $response;
    }

    protected function addCorsHeaders(Response $response, Request $request): void
    {
        $origin = $request->header('Origin');
        
        if ($this->isOriginAllowed($origin)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin ?? '*');
        }

        $response->setHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods));
        $response->setHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders));
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Max-Age', '86400');
    }

    protected function isOriginAllowed(?string $origin): bool
    {
        if (in_array('*', $this->allowedOrigins)) {
            return true;
        }

        return in_array($origin, $this->allowedOrigins);
    }

    public function setAllowedOrigins(array $origins): void
    {
        $this->allowedOrigins = $origins;
    }

    public function setAllowedMethods(array $methods): void
    {
        $this->allowedMethods = $methods;
    }

    public function setAllowedHeaders(array $headers): void
    {
        $this->allowedHeaders = $headers;
    }
}
