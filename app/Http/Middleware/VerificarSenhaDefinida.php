<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSenhaDefinida
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user() &&
            is_null($request->user()->password) &&
            !$request->routeIs('senha.definir.*')
        ) {
            return redirect()->route('senha.definir.mostrar');
        }

        return $next($request);
    }
}