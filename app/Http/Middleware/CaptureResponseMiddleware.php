<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Opentelemetry\Context\Context;
use Opentelemetry\SDK\Trace\Span;

class CaptureResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $currentContext = Context::getCurrent();
        $currentSpan = Span::fromContext($currentContext);

        if($currentSpan != null && method_exists($currentSpan, 'setAttribute')) {
            
            $responseBody = $response->getContent();
            $currentSpan->setAttribute('http.response.body', $responseBody);
        }
        return $response;
    }
}