<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $end = microtime(true);
        $executionTime = ($end - $start) * 1000; // Convert to milliseconds

        $p90 =
        // Log the execution time
        Log::info('Execution time: ' . $executionTime . 'ms', [
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'execution_time' => $executionTime,
        ]);

        return $response;
    }
}
