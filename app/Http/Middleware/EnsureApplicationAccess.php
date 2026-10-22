<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnsureApplicationAccess
{
    public function handle(Request $request, Closure $next, string $appCode): Response
    {
        $applications = $request->attributes->get('auth_applications', []);

        if (! in_array($appCode, $applications, true)) {
            throw new HttpException(403, 'Acesso não autorizado a esta aplicação.');
        }

        return $next($request);
    }
}
