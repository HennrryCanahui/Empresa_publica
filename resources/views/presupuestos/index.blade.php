@extends('layouts.app')

@section('title', 'Presupuestos')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-cash"></i> Validación de Presupuestos</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('presupuestos.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="buscar" 
                           value="{{ request('buscar') }}" 
                           placeholder="Buscar por número de solicitud">
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
                <select class="form-select" name="validacion">
                    <option value="">-- Todas las validaciones --</option>
                    @foreach(['PENDIENTE' => 'Pendiente', 'APROBADA' => 'Aprobada', 'RECHAZADA' => 'Rechazada'] as $key => $value)
                    <option value="{{ $key }}" {{ request('validacion') == $key ? 'selected' : '' }}>
                        {{ $value }}
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
                        <th>Solicitud</th>
                        <th>Unidad</th>
                        <th>Monto Est.</th>
                        <th>Validación</th>
                        <th>Fecha Val.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presupuestos as $presupuesto)
                    <tr>
                        <td>
                            <a href="{{ route('solicitudes.show', $presupuesto->solicitud) }}">
                                {{ $presupuesto->solicitud->numero_solicitud }}
                            </a>
                        </td>
                        <td>{{ $presupuesto->solicitud->unidadSolicitante->nombre }}</td>
                        <td>{{ number_format($presupuesto->monto_estimado, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $validaciones_color[$presupuesto->validacion] ?? 'secondary' }}">
                                {{ $presupuesto->validacion }}
                            </span>
                        </td>
                        <td>{{ $presupuesto->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($presupuesto->validacion == 'PENDIENTE')
                            <a href="{{ route('presupuestos.validar', $presupuesto->solicitud) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-check-circle"></i> Validar
                            </a>
                            @else
                            <button type="button" 
                                    class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetalles{{ $presupuesto->id_presupuesto }}">
                                <i class="bi bi-eye"></i> Ver Detalles
                            </button>

                            <!-- Modal Detalles -->
                            <div class="modal fade" id="modalDetalles{{ $presupuesto->id_presupuesto }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detalles de Validación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Validación:</strong> {{ $presupuesto->validacion }}</p>
                                            <p><strong>Partida:</strong> {{ $presupuesto->partida_presupuestaria }}</p>
                                            <p><strong>Disponibilidad:</strong> 
                                                {{ number_format($presupuesto->disponibilidad_actual, 2) }}
                                            </p>
                                            <p><strong>Validado por:</strong> 
                                                {{ $presupuesto->usuarioPresupuesto->nombre }}
                                            </p>
                                            @if($presupuesto->observaciones)
                                            <p><strong>Observaciones:</strong><br>
                                                {{ $presupuesto->observaciones }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron registros</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $presupuestos->links() }}
        </div>
    </div>
</div>
@endsection
