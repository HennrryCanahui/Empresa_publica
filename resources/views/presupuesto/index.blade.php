  @extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-cash-coin me-2"></i>Validación Presupuestaria</h2>
    <a href="{{ route('presupuesto.historial') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Ver Historial
    </a>
</div>
@endsection

@section('content')
<!-- Resumen de estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-hourglass-split display-4 text-warning"></i>
                <h3 class="mt-2">{{ $solicitudes->total() }}</h3>
                <p class="text-muted mb-0">Pendientes de Validar</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                <h3 class="mt-2">{{ $solicitudes->where('prioridad', 'Urgente')->count() }}</h3>
                <p class="text-muted mb-0">Urgentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-currency-dollar display-4 text-success"></i>
                <h3 class="mt-2">Q {{ number_format($solicitudes->sum('monto_total_estimado'), 2) }}</h3>
                <p class="text-muted mb-0">Monto Total Solicitado</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de solicitudes -->
<div class="card">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Solicitudes Pendientes de Validación</h5>
      <span class="badge bg-primary">{{ $solicitudes->total() }} solicitudes</span>
    </div>
  </div>
  <div class="card-body p-0">
    @if($solicitudes->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Solicitud</th>
              <th>Unidad/Solicitante</th>
              <th>Descripción</th>
              <th>Prioridad</th>
              <th>Monto Est.</th>
              <th>Items</th>
              <th>Fecha</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($solicitudes as $s)
              <tr class="{{ $s->prioridad == 'Urgente' ? 'table-danger' : '' }}">
                <td>
                  <strong>{{ $s->numero_solicitud }}</strong>
                  @if($s->prioridad == 'Urgente')
                    <span class="badge bg-danger ms-1">URGENTE</span>
                  @endif
                </td>
                <td>
                  <div><strong>{{ $s->unidadSolicitante->nombre_unidad ?? 'N/A' }}</strong></div>
                  <small class="text-muted">
                    {{ trim(($s->usuarioCreador->nombre ?? '') . ' ' . ($s->usuarioCreador->apellido ?? '')) ?: 'N/A' }}
                  </small>
                </td>
                <td>
                  <div class="text-truncate" style="max-width:250px" title="{{ $s->descripcion }}">
                    {{ $s->descripcion }}
                  </div>
                </td>
                <td>
                  @if($s->prioridad == 'Urgente')
                    <span class="badge bg-danger">{{ $s->prioridad }}</span>
                  @elseif($s->prioridad == 'Alta')
                    <span class="badge bg-warning text-dark">{{ $s->prioridad }}</span>
                  @elseif($s->prioridad == 'Media')
                    <span class="badge bg-info">{{ $s->prioridad }}</span>
                  @else
                    <span class="badge bg-secondary">{{ $s->prioridad }}</span>
                  @endif
                </td>
                <td class="fw-bold">Q {{ number_format($s->monto_total_estimado ?? 0, 2) }}</td>
                <td>
                  <span class="badge bg-secondary">{{ $s->detalles->count() }} productos</span>
                </td>
                <td>
                  <small>{{ \Carbon\Carbon::parse($s->fecha_creacion)->format('d/m/Y') }}</small><br>
                  <small class="text-muted">{{ \Carbon\Carbon::parse($s->fecha_creacion)->diffForHumans() }}</small>
                </td>
                <td class="text-center">
                  <a class="btn btn-sm btn-primary" href="{{ route('presupuesto.validar', $s->id_solicitud) }}" title="Validar presupuesto">
                    <i class="bi bi-check2-square me-1"></i>Validar
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($solicitudes->hasPages())
      <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-muted">
            Mostrando {{ $solicitudes->firstItem() }} - {{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} solicitudes
          </div>
          <div>
            {{ $solicitudes->links() }}
          </div>
        </div>
      </div>
      @endif
    @else
      <div class="p-5 text-center">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted">No hay solicitudes pendientes de validación presupuestaria</p>
        <a href="{{ route('presupuesto.historial') }}" class="btn btn-outline-primary">Ver Historial</a>
      </div>
    @endif
  </div>
</div>
@endsection