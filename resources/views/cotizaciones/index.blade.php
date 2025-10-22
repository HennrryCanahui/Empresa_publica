@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-receipt"></i> Cotizaciones</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('cotizaciones.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="buscar" 
                           value="{{ request('buscar') }}" 
                           placeholder="Buscar por número">
                </div>
            </div>
            
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">-- Todos los estados --</option>
                    @foreach($estados as $key => $estado)
                    <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                        {{ $estado }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-4">
                <select class="form-select" name="proveedor">
                    <option value="">-- Todos los proveedores --</option>
                    @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id_proveedor }}" 
                            {{ request('proveedor') == $proveedor->id_proveedor ? 'selected' : '' }}>
                        {{ $proveedor->razon_social }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
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
                        <th>Solicitud</th>
                        <th>Proveedor</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cotizaciones as $cotizacion)
                    <tr>
                        <td>{{ $cotizacion->numero_cotizacion }}</td>
                        <td>
                            <a href="{{ route('solicitudes.show', $cotizacion->solicitud) }}">
                                {{ $cotizacion->solicitud->numero_solicitud }}
                            </a>
                        </td>
                        <td>{{ $cotizacion->proveedor->razon_social }}</td>
                        <td>{{ number_format($cotizacion->monto_total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $estados_color[$cotizacion->estado] ?? 'secondary' }}">
                                {{ $cotizacion->estado }}
                            </span>
                        </td>
                        <td>{{ $cotizacion->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                @if($cotizacion->archivo_cotizacion)
                                <a href="{{ route('cotizaciones.download', $cotizacion) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip"
                                   title="Descargar PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                @endif

                                @if($cotizacion->solicitud->estado == 'Cotizada')
                                <a href="{{ route('cotizaciones.comparar', $cotizacion->solicitud) }}" 
                                   class="btn btn-sm btn-outline-info"
                                   data-bs-toggle="tooltip"
                                   title="Comparar Cotizaciones">
                                    <i class="bi bi-table"></i>
                                </a>
                                @endif

                                @if($cotizacion->estado == 'Seleccionada' && !$cotizacion->adquisicion)
                                <a href="{{ route('adquisiciones.create', ['solicitud' => $cotizacion->solicitud, 'cotizacion' => $cotizacion]) }}" 
                                   class="btn btn-sm btn-outline-success"
                                   data-bs-toggle="tooltip"
                                   title="Generar Orden de Compra">
                                    <i class="bi bi-bag"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron cotizaciones</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $cotizaciones->links() }}
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
