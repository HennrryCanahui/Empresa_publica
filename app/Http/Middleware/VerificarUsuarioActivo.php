<?php
// app/Http/Middleware/VerificarUsuarioActivo.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerificarUsuarioActivo
{
    /**
     * Verifica que el usuario esté activo (campo activo = 1)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->activo == 0) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        }
        
        return $next($request);
    }
}