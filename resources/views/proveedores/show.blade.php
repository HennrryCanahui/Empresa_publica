@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-eye me-2"></i>Detalle del Proveedor</h2>
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Listado
    </a>
    <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i>Editar
    </a>
</div>

<!-- Información Principal -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-building me-2"></i>Información del Proveedor
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="bi bi-tag text-primary me-2"></i>Código:</strong></p>
                        <p class="ms-4"><span class="badge bg-secondary fs-6">{{ $proveedor->codigo }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="bi bi-card-text text-primary me-2"></i>NIT:</strong></p>
                        <p class="ms-4">{{ $proveedor->nit_rfc }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <p class="mb-1"><strong><i class="bi bi-building text-primary me-2"></i>Razón Social:</strong></p>
                        <p class="ms-4 fs-5">{{ $proveedor->razon_social }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <p class="mb-1"><strong><i class="bi bi-geo-alt text-primary me-2"></i>Dirección:</strong></p>
                        <p class="ms-4">{{ $proveedor->direccion ?? 'No especificada' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="bi bi-telephone text-primary me-2"></i>Teléfono:</strong></p>
                        <p class="ms-4">{{ $proveedor->telefono ?? 'No especificado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="bi bi-envelope text-primary me-2"></i>Correo:</strong></p>
                        <p class="ms-4">{{ $proveedor->correo ?? 'No especificado' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="bi bi-person text-primary me-2"></i>Contacto Principal:</strong></p>
                        <p class="ms-4">{{ $proveedor->contacto_principal ?? 'No especificado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong><i class="bi bi-check-circle text-primary me-2"></i>Estado:</strong></p>
                        <p class="ms-4">
                            @if($proveedor->activo)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactivo</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Estadísticas -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <i class="bi bi-graph-up me-2"></i>Estadísticas
            </div>
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-text display-4 text-info mb-3"></i>
                <h3 class="mb-1">{{ $proveedor->cotizaciones->count() }}</h3>
                <p class="text-muted mb-0">Cotizaciones Realizadas</p>
            </div>
        </div>

        <!-- Información de Registro -->
        <div class="card">
            <div class="card-header bg-white">
                <i class="bi bi-info-circle me-2"></i>Información de Registro
            </div>
            <div class="card-body">
                <p class="mb-2"><small class="text-muted"><i class="bi bi-calendar-plus me-1"></i>Creado:</small></p>
                <p class="ms-3"><small>{{ $proveedor->created_at ? $proveedor->created_at->format('d/m/Y H:i') : 'N/A' }}</small></p>
                
                <p class="mb-2"><small class="text-muted"><i class="bi bi-calendar-check me-1"></i>Última actualización:</small></p>
                <p class="ms-3"><small>{{ $proveedor->updated_at ? $proveedor->updated_at->format('d/m/Y H:i') : 'N/A' }}</small></p>
            </div>
        </div>
    </div>
</div>

<!-- Cotizaciones -->
<div class="card">
    <div class="card-header bg-white">
        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Cotizaciones del Proveedor
    </div>
    <div class="card-body">
        @if($proveedor->cotizaciones->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Solicitud</th>
                            <th>Monto Total</th>
                            <th>Tiempo Entrega</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedor->cotizaciones as $cotizacion)
                            <tr>
                                <td><small>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</small></td>
                                <td><small>{{ $cotizacion->solicitud->numero_solicitud ?? 'N/A' }}</small></td>
                                <td class="fw-bold text-success">Q {{ number_format($cotizacion->monto_total, 2) }}</td>
                                <td><small>{{ $cotizacion->tiempo_entrega ?? 'N/A' }}</small></td>
                                <td>
                                    @if($cotizacion->estado == 'Seleccionada')
                                        <span class="badge bg-success">Seleccionada</span>
                                    @elseif($cotizacion->estado == 'Descartada')
                                        <span class="badge bg-secondary">Descartada</span>
                                    @else
                                        <span class="badge bg-info">{{ $cotizacion->estado }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('compras.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="text-muted mt-3">Este proveedor aún no ha realizado cotizaciones</p>
            </div>
        @endif
    </div>
</div>
@endsection
