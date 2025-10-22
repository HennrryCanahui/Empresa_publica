<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Models\DocumentoAdjunto;
use App\Policies\SolicitudPolicy;
use App\Policies\DocumentoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Solicitud::class => SolicitudPolicy::class,
        DocumentoAdjunto::class => DocumentoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir capacidades por rol
        Gate::define('crear-solicitud', function (Usuario $usuario) {
            return $usuario->rol === 'solicitante';
        });

        Gate::define('validar-presupuesto', function (Usuario $usuario) {
            return $usuario->rol === 'presupuesto';
        });

        Gate::define('gestionar-cotizaciones', function (Usuario $usuario) {
            return $usuario->rol === 'compras';
        });

        Gate::define('aprobar-solicitud', function (Usuario $usuario) {
            return $usuario->rol === 'aprobador';
        });

        Gate::define('gestionar-usuarios', function (Usuario $usuario) {
            return $usuario->rol === 'admin';
        });

        Gate::define('ver-reportes', function (Usuario $usuario) {
            return in_array($usuario->rol, ['admin', 'aprobador']);
        });

        Gate::define('gestionar-proveedores', function (Usuario $usuario) {
            return in_array($usuario->rol, ['admin', 'compras']);
        });

        Gate::define('ver-auditoria', function (Usuario $usuario) {
            return $usuario->rol === 'admin';
        });
    }
}