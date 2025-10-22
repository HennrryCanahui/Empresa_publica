@extends('layouts.app')

@section('title', 'Solicitudes')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-file-text"></i> Solicitudes</h2>
    </div>
    <div class="col-md-4 text-end">
        @can('crear-solicitudes')
        <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Solicitud
        </a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('solicitudes.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="buscar" 
                           value="{{ request('buscar') }}" 
                           placeholder="Buscar por número">
                </div>
            </div>
            
            <div class="col-md-3">
                <select class="form-select" name="unidad">
                    <option value="">-- Todas las unidades --</option>
                    @foreach($unidades as $unidad)
                    <option value="{{ $unidad->id_unidad }}" 
                            {{ request('unidad') == $unidad->id_unidad ? 'selected' : '' }}>
                        {{ $unidad->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">-- Todos los estados --</option>
                    @foreach($estados as $key => $estado)
                    <option value="{{ $key }}" 
                            {{ request('estado') == $key ? 'selected' : '' }}>
                        {{ $estado }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Unidad</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Monto Est.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->numero_solicitud }}</td>
                        <td>{{ $solicitud->unidadSolicitante->nombre }}</td>
                        <td>{{ Str::limit($solicitud->descripcion, 50) }}</td>
                        <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $estados_color[$solicitud->estado] ?? 'secondary' }}">
                                {{ $solicitud->estado }}
                            </span>
                        </td>
                        <td>{{ number_format($solicitud->monto_estimado_total, 2) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('solicitudes.show', $solicitud) }}" 
                                   class="btn btn-sm btn-outline-info"
                                   data-bs-toggle="tooltip"
                                   title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($solicitud->estado == 'Borrador' && Auth::id() == $solicitud->id_usuario_creador)
                                <a href="{{ route('solicitudes.edit', $solicitud) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                @can('validar-presupuesto')
                                @if($solicitud->estado == 'Pendiente')
                                <a href="{{ route('presupuestos.validar', $solicitud) }}"
                                   class="btn btn-sm btn-outline-warning"
                                   data-bs-toggle="tooltip"
                                   title="Validar Presupuesto">
                                    <i class="bi bi-cash"></i>
                                </a>
                                @endif
                                @endcan

                                @can('crear-cotizaciones')
                                @if($solicitud->estado == 'Presupuesto Validado')
                                <a href="{{ route('cotizaciones.create', $solicitud) }}"
                                   class="btn btn-sm btn-outline-success"
                                   data-bs-toggle="tooltip"
                                   title="Crear Cotización">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                @endif
                                @endcan

                                @can('evaluar-solicitudes')
                                @if($solicitud->estado == 'Cotizada')
                                <a href="{{ route('aprobaciones.evaluar', $solicitud) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip"
                                   title="Evaluar Solicitud">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                                @endif
                                @endcan

                                @can('crear-adquisiciones')
                                @if($solicitud->estado == 'Aprobada')
                                <a href="{{ route('adquisiciones.create', $solicitud) }}"
                                   class="btn btn-sm btn-outline-success"
                                   data-bs-toggle="tooltip"
                                   title="Generar Orden de Compra">
                                    <i class="bi bi-bag"></i>
                                </a>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron solicitudes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $solicitudes->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush
@endsection
