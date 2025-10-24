@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
@endsection

@section('content')
@php
    $user = Auth::user();
    $rol = $user->rol ?? 'Solicitante';
@endphp

{{-- DASHBOARD SOLICITANTE --}}
@if($rol === 'Solicitante')
<div class="mb-4">
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Bienvenido {{ $user->nombre }}</strong> - Desde aquí puedes gestionar tus solicitudes de compra.
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-folder2-open display-4 text-primary mb-3"></i>
                <h5 class="card-title">Mis Solicitudes</h5>
                <p class="card-text text-muted">Ver todas mis solicitudes</p>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i>Ver Todas
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-plus-circle display-4 text-success mb-3"></i>
                <h5 class="card-title">Nueva Solicitud</h5>
                <p class="card-text text-muted">Crear solicitud de compra</p>
                <a href="{{ route('solicitudes.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg me-1"></i>Crear
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-clock-history display-4 text-warning mb-3"></i>
                <h5 class="card-title">Historial</h5>
                <p class="card-text text-muted">Consultar historial completo</p>
                <a href="{{ route('solicitudes.historial') }}" class="btn btn-warning">
                    <i class="bi bi-clock-history me-1"></i>Historial
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-person-circle display-4 text-info mb-3"></i>
                <h5 class="card-title">Mi Perfil</h5>
                <p class="card-text text-muted">Actualizar información</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-info">
                    <i class="bi bi-person me-1"></i>Ver Perfil
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- DASHBOARD PRESUPUESTO --}}
@if($rol === 'Presupuesto')
<div class="mb-4">
    <div class="alert alert-warning">
        <i class="bi bi-calculator me-2"></i>
        <strong>Bienvenido {{ $user->nombre }}</strong> - Área de Validación Presupuestaria.
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-split display-4 text-warning mb-3"></i>
                <h5 class="card-title">Pendientes</h5>
                <p class="card-text text-muted">Solicitudes por validar</p>
                <a href="{{ route('presupuesto.index') }}" class="btn btn-warning">
                    <i class="bi bi-list-check me-1"></i>Ver Pendientes
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-clock-history display-4 text-info mb-3"></i>
                <h5 class="card-title">Historial</h5>
                <p class="card-text text-muted">Validaciones realizadas</p>
                <a href="{{ route('presupuesto.historial') }}" class="btn btn-info">
                    <i class="bi bi-clock-history me-1"></i>Ver Historial
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                <h5 class="card-title">Reportes</h5>
                <p class="card-text text-muted">Estadísticas y métricas</p>
                <a href="{{ route('presupuesto.historial') }}" class="btn btn-primary">
                    <i class="bi bi-bar-chart me-1"></i>Ver Reportes
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- DASHBOARD COMPRAS --}}
@if($rol === 'Compras')
<div class="mb-4">
    <div class="alert alert-info">
        <i class="bi bi-cart-check me-2"></i>
        <strong>Bienvenido {{ $user->nombre }}</strong> - Área de Cotizaciones y Compras.
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-receipt display-4 text-primary mb-3"></i>
                <h5 class="card-title">Cotizaciones</h5>
                <p class="card-text text-muted">Gestionar cotizaciones</p>
                <a href="{{ route('compras.index') }}" class="btn btn-primary">
                    <i class="bi bi-receipt-cutoff me-1"></i>Ver Todas
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-people display-4 text-success mb-3"></i>
                <h5 class="card-title">Proveedores</h5>
                <p class="card-text text-muted">Administrar proveedores</p>
                <a href="{{ route('proveedores.index') }}" class="btn btn-success">
                    <i class="bi bi-building me-1"></i>Ver Proveedores
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up display-4 text-info mb-3"></i>
                <h5 class="card-title">Estadísticas</h5>
                <p class="card-text text-muted">Métricas de compras</p>
                <a href="{{ route('compras.index') }}" class="btn btn-info">
                    <i class="bi bi-bar-chart me-1"></i>Ver Estadísticas
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- DASHBOARD AUTORIDAD --}}
@if($rol === 'Autoridad')
<div class="mb-4">
    <div class="alert alert-success">
        <i class="bi bi-shield-check me-2"></i>
        <strong>Bienvenido {{ $user->nombre }}</strong> - Área de Aprobación de Autoridad.
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-check display-4 text-success mb-3"></i>
                <h5 class="card-title">Pendientes</h5>
                <p class="card-text text-muted">Solicitudes por aprobar</p>
                <a href="{{ route('aprobacion.index') }}" class="btn btn-success">
                    <i class="bi bi-list-check me-1"></i>Ver Pendientes
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-clock-history display-4 text-primary mb-3"></i>
                <h5 class="card-title">Historial</h5>
                <p class="card-text text-muted">Decisiones tomadas</p>
                <a href="{{ route('aprobacion.historial') }}" class="btn btn-primary">
                    <i class="bi bi-clock-history me-1"></i>Ver Historial
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-bar-graph display-4 text-info mb-3"></i>
                <h5 class="card-title">Reportes</h5>
                <p class="card-text text-muted">Métricas de aprobación</p>
                <a href="{{ route('aprobacion.historial') }}" class="btn btn-info">
                    <i class="bi bi-bar-chart me-1"></i>Ver Reportes
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- DASHBOARD ADMIN --}}
@if($rol === 'Admin')
<div class="mb-4">
    <div class="alert alert-danger">
        <i class="bi bi-gear-fill me-2"></i>
        <strong>Bienvenido {{ $user->nombre }}</strong> - Panel de Administración del Sistema.
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-people-fill display-4 text-primary mb-3"></i>
                <h5 class="card-title">Usuarios</h5>
                <p class="card-text text-muted">Gestionar usuarios</p>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary">
                    <i class="bi bi-person-gear me-1"></i>Administrar
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-building display-4 text-success mb-3"></i>
                <h5 class="card-title">Unidades</h5>
                <p class="card-text text-muted">Gestionar unidades</p>
                <a href="{{ route('admin.unidades.index') }}" class="btn btn-success">
                    <i class="bi bi-building me-1"></i>Administrar
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-box-seam display-4 text-warning mb-3"></i>
                <h5 class="card-title">Productos</h5>
                <p class="card-text text-muted">Catálogo de productos</p>
                <a href="{{ route('admin.productos.index') }}" class="btn btn-warning">
                    <i class="bi bi-box-seam me-1"></i>Administrar
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-tags display-4 text-info mb-3"></i>
                <h5 class="card-title">Categorías</h5>
                <p class="card-text text-muted">Categorías de productos</p>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-info">
                    <i class="bi bi-tags me-1"></i>Administrar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-data display-4 text-secondary mb-3"></i>
                <h5 class="card-title">Solicitudes</h5>
                <p class="card-text text-muted">Ver todas las solicitudes</p>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
                    <i class="bi bi-folder2-open me-1"></i>Ver Todas
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-people display-4 text-dark mb-3"></i>
                <h5 class="card-title">Proveedores</h5>
                <p class="card-text text-muted">Gestionar proveedores</p>
                <a href="{{ route('proveedores.index') }}" class="btn btn-dark">
                    <i class="bi bi-building me-1"></i>Administrar
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-graph-up-arrow display-4 text-primary mb-3"></i>
                <h5 class="card-title">Reportes</h5>
                <p class="card-text text-muted">Estadísticas generales</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-bar-chart me-1"></i>Ver Reportes
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
