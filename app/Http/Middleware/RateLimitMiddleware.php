<?php

namespace App\Http\Middleware;

use Celovel\Http\Middleware\Middleware;
use Celovel\Http\Request;
use Celovel\Http\Response;

class RateLimitMiddleware extends Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        // Middleware logic here
        
        return $next($request);
    }
}
