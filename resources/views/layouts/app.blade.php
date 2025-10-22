<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Adquisiciones')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-building"></i> Sistema de Adquisiciones
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    @can('ver-solicitudes')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('solicitudes.index') }}">
                            <i class="bi bi-file-text"></i> Solicitudes
                        </a>
                    </li>
                    @endcan
                    
                    @can('ver-presupuestos')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('presupuestos.index') }}">
                            <i class="bi bi-cash"></i> Presupuestos
                        </a>
                    </li>
                    @endcan
                    
                    @can('ver-cotizaciones')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cotizaciones.index') }}">
                            <i class="bi bi-receipt"></i> Cotizaciones
                        </a>
                    </li>
                    @endcan
                    
                    @can('ver-aprobaciones')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('aprobaciones.index') }}">
                            <i class="bi bi-check-circle"></i> Aprobaciones
                        </a>
                    </li>
                    @endcan
                    
                    @can('ver-adquisiciones')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('adquisiciones.index') }}">
                            <i class="bi bi-bag"></i> Adquisiciones
                        </a>
                    </li>
                    @endcan
                    
                    @can('ver-catalogos')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Catálogos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('productos.index') }}">Productos</a></li>
                            <li><a class="dropdown-item" href="{{ route('proveedores.index') }}">Proveedores</a></li>
                            <li><a class="dropdown-item" href="{{ route('unidades.index') }}">Unidades</a></li>
                            @can('ver-usuarios')
                            <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">Usuarios</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    
                    @can('ver-reportes')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-graph-up"></i> Reportes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reportes.index') }}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('auditoria.index') }}">Auditoría</a></li>
                        </ul>
                    </li>
                    @endcan
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->nombre }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('perfil.index') }}">Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <main class="container py-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>