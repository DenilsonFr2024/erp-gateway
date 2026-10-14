<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonContentType
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('api/*')) {
            return $next($request);
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            return $next($request);
        }

        if ($request->getContent() === '') {
            return $next($request);
        }

        if (! $request->isJson()) {
            abort(415);
        }

        return $next($request);
    }
}
