<?php

namespace App\Http\Middleware;

use Celovel\Http\Middleware\Middleware;
use Celovel\Http\Request;
use Celovel\Http\Response;

class LoggingMiddleware extends Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Request'i logla
        $this->logRequest($request);
        
        // Response'u al
        $response = $next($request);
        
        // Response'u logla
        $this->logResponse($response, $startTime);
        
        return $response;
    }

    protected function logRequest(Request $request): void
    {
        $log = sprintf(
            "[%s] %s %s - IP: %s - User-Agent: %s",
            date('Y-m-d H:i:s'),
            $request->getMethod(),
            $request->getPath(),
            $request->header('X-Forwarded-For') ?? $request->header('X-Real-IP') ?? 'unknown',
            $request->header('User-Agent') ?? 'unknown'
        );
        
        error_log($log);
    }

    protected function logResponse(Response $response, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $log = sprintf(
            "[%s] Response: %d - Duration: %sms",
            date('Y-m-d H:i:s'),
            $response->getStatusCode(),
            $duration
        );
        
        error_log($log);
    }
}
