@extends('layouts.app')

@section('title', 'Adquisiciones')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-bag"></i> Órdenes de Compra</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('adquisiciones.index') }}" method="GET" class="row g-3 mb-4">
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
                        <th>Orden #</th>
                        <th>Solicitud</th>
                        <th>Proveedor</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adquisiciones as $adquisicion)
                    <tr>
                        <td>{{ $adquisicion->numero_orden_compra }}</td>
                        <td>
                            <a href="{{ route('solicitudes.show', $adquisicion->solicitud) }}">
                                {{ $adquisicion->solicitud->numero_solicitud }}
                            </a>
                        </td>
                        <td>{{ $adquisicion->proveedor->razon_social }}</td>
                        <td>${{ number_format($adquisicion->monto_total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $estados_color[$adquisicion->estado_entrega] ?? 'secondary' }}">
                                {{ $adquisicion->estado_entrega }}
                            </span>
                        </td>
                        <td>{{ $adquisicion->fecha_orden->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                @if($adquisicion->archivo_orden)
                                <a href="{{ route('adquisiciones.download', $adquisicion) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip"
                                   title="Descargar Orden">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                @endif

                                @if($adquisicion->estado_entrega != 'ENTREGADO')
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEstado{{ $adquisicion->id_adquisicion }}">
                                    <i class="bi bi-truck"></i>
                                </button>

                                <!-- Modal Actualizar Estado -->
                                <div class="modal fade" id="modalEstado{{ $adquisicion->id_adquisicion }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('adquisiciones.actualizar-estado', $adquisicion) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Actualizar Estado de Entrega</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="estado_entrega" class="form-label">Estado *</label>
                                                        <select class="form-select" id="estado_entrega" name="estado_entrega" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach($estados as $key => $estado)
                                                            <option value="{{ $key }}" 
                                                                    {{ $adquisicion->estado_entrega == $key ? 'selected' : '' }}>
                                                                {{ $estado }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="observaciones_entrega" class="form-label">Observaciones</label>
                                                        <textarea class="form-control" id="observaciones_entrega" 
                                                                  name="observaciones_entrega" rows="3">{{ $adquisicion->observaciones_entrega }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron órdenes de compra</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $adquisiciones->links() }}
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