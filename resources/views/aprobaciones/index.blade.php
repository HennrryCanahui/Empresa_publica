@extends('layouts.app')

@section('title', 'Aprobaciones')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-check-circle"></i> Aprobaciones</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('aprobaciones.index') }}" method="GET" class="row g-3 mb-4">
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
                <select class="form-select" name="decision">
                    <option value="">-- Todas las decisiones --</option>
                    @foreach(['APROBADA' => 'Aprobada', 'RECHAZADA' => 'Rechazada', 'PENDIENTE' => 'Pendiente'] as $key => $value)
                    <option value="{{ $key }}" {{ request('decision') == $key ? 'selected' : '' }}>
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
                        <th>Monto</th>
                        <th>Decisión</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aprobaciones as $aprobacion)
                    <tr>
                        <td>
                            <a href="{{ route('solicitudes.show', $aprobacion->solicitud) }}">
                                {{ $aprobacion->solicitud->numero_solicitud }}
                            </a>
                        </td>
                        <td>{{ $aprobacion->solicitud->unidadSolicitante->nombre }}</td>
                        <td>{{ number_format($aprobacion->monto_aprobado, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $decisiones_color[$aprobacion->decision] ?? 'secondary' }}">
                                {{ $aprobacion->decision }}
                            </span>
                        </td>
                        <td>{{ $aprobacion->fecha_aprobacion->format('d/m/Y') }}</td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetalles{{ $aprobacion->id_aprobacion }}">
                                <i class="bi bi-eye"></i> Ver Detalles
                            </button>

                            <!-- Modal Detalles -->
                            <div class="modal fade" id="modalDetalles{{ $aprobacion->id_aprobacion }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detalles de Aprobación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Decisión:</strong> {{ $aprobacion->decision }}</p>
                                            <p><strong>Monto Aprobado:</strong> 
                                                ${{ number_format($aprobacion->monto_aprobado, 2) }}
                                            </p>
                                            <p><strong>Autoridad:</strong> 
                                                {{ $aprobacion->usuarioAutoridad->nombre }}
                                            </p>
                                            @if($aprobacion->condiciones_aprobacion)
                                            <p><strong>Condiciones:</strong><br>
                                                {{ $aprobacion->condiciones_aprobacion }}
                                            </p>
                                            @endif
                                            @if($aprobacion->observaciones)
                                            <p><strong>Observaciones:</strong><br>
                                                {{ $aprobacion->observaciones }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron aprobaciones</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $aprobaciones->links() }}
        </div>
    </div>
</div>
@endsection
