@extends('layouts.app')

@section('title', 'Dashboard Solicitante')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard de Solicitante</h1>
    
    <div class="row">
        <!-- Solicitudes Pendientes -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mis Solicitudes Pendientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesPendientes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->id }}</td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $solicitud->estado_color }}">
                                            {{ $solicitud->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('solicitudes.show', $solicitud->id) }}" 
                                           class="btn btn-sm btn-info">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mis Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="border rounded p-3 text-center">
                                <h6>Total Solicitudes</h6>
                                <h3>{{ $estadisticas['total'] }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 text-center">
                                <h6>En Proceso</h6>
                                <h3>{{ $estadisticas['en_proceso'] }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 text-center">
                                <h6>Aprobadas</h6>
                                <h3>{{ $estadisticas['aprobadas'] }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 text-center">
                                <h6>Rechazadas</h6>
                                <h3>{{ $estadisticas['rechazadas'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nueva Solicitud
                        </a>
                        <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-list"></i> Ver Todas las Solicitudes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection