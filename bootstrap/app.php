<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckPermission;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrar alias de middleware personalizados
        $middleware->alias([
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
        ]);
        $middleware->web(append: [
        \App\Http\Middleware\VerificarUsuarioActivo::class,
    ]);
        
        // Opcional: Middleware global (se aplica a todas las rutas web)
        // $middleware->web(append: [
        //     \App\Http\Middleware\VerificarUsuarioActivo::class,
        // ]);
        
        // Opcional: Prioridad de middleware
        // $middleware->priority([
        //     \Illuminate\Session\Middleware\StartSession::class,
        //     \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        //     CheckRole::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejo personalizado de excepciones 403 (sin permiso)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [
                    'message' => $e->getMessage()
                ], 403);
            }
        });
    })->create();