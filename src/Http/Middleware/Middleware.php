<?php

namespace Celovel\Http\Middleware;

use Celovel\Http\Request;
use Celovel\Http\Response;

abstract class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    abstract public function handle(Request $request, \Closure $next): Response;

    /**
     * Get the middleware parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [];
    }

    /**
     * Set middleware parameters.
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        // Override in child classes if needed
    }
}
