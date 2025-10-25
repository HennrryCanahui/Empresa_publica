@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-ui-checks-grid me-2"></i>Solicitudes para Cotización</h2>
    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Panel
    </a>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="bi bi-clipboard-check display-4 text-primary"></i>
                <h3 class="mt-2">{{ $solicitudes->where('estado', 'Presupuestada')->count() }}</h3>
                <p class="text-muted mb-0">Sin Cotizaciones</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="bi bi-file-earmark-text display-4 text-info"></i>
                <h3 class="mt-2">{{ $solicitudes->where('estado', 'En_Cotizacion')->count() }}</h3>
                <p class="text-muted mb-0">En Cotización</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                <h3 class="mt-2">{{ $solicitudes->where('prioridad', 'Urgente')->count() }}</h3>
                <p class="text-muted mb-0">Urgentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="bi bi-currency-dollar display-4 text-success"></i>
                <h3 class="mt-2">Q {{ number_format($solicitudes->sum('monto_total_estimado'), 2) }}</h3>
                <p class="text-muted mb-0">Monto Total</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-table me-2"></i>Solicitudes Pendientes de Cotización</h5>
      <span class="badge bg-primary">{{ $solicitudes->total() }} solicitudes</span>
    </div>
  </div>
  <div class="card-body p-0">
    @if($solicitudes->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th>Número</th>
              <th>Unidad</th>
              <th>Descripción</th>
              <th>Estado</th>
              <th>Prioridad</th>
              <th>Monto Est.</th>
              <th>Cotizaciones</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($solicitudes as $s)
            <tr>
              <td>
                <small>{{ $s->fecha_creacion->format('d/m/Y') }}</small><br>
                <small class="text-muted">{{ $s->fecha_creacion->diffForHumans() }}</small>
              </td>
              <td>
                <strong class="text-primary">{{ $s->numero_solicitud }}</strong>
              </td>
              <td>
                <small>{{ $s->unidadSolicitante->nombre ?? 'N/A' }}</small><br>
                <small class="text-muted">{{ $s->usuarioCreador->name ?? 'N/A' }}</small>
              </td>
              <td>
                <span data-bs-toggle="tooltip" title="{{ $s->descripcion }}">
                  {{ Str::limit($s->descripcion, 50) }}
                </span>
              </td>
              <td>
                @if($s->estado == 'Presupuestada')
                  <span class="badge bg-primary">Presupuestada</span>
                @elseif($s->estado == 'En_Cotizacion')
                  <span class="badge bg-info">En Cotización</span>
                @else
                  <span class="badge bg-secondary">{{ str_replace('_', ' ', $s->estado) }}</span>
                @endif
              </td>
              <td>
                @if($s->prioridad == 'Urgente')
                  <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Urgente</span>
                @elseif($s->prioridad == 'Alta')
                  <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Alta</span>
                @elseif($s->prioridad == 'Media')
                  <span class="badge bg-info">Media</span>
                @else
                  <span class="badge bg-secondary">Baja</span>
                @endif
              </td>
              <td class="fw-bold text-success">Q {{ number_format($s->monto_total_estimado ?? 0, 2) }}</td>
              <td class="text-center">
                @if($s->cotizaciones->count() > 0)
                  <span class="badge bg-success">{{ $s->cotizaciones->count() }}</span>
                @else
                  <span class="badge bg-secondary">0</span>
                @endif
              </td>
              <td class="text-center">
                <div class="btn-group" role="group">
                  <a class="btn btn-sm btn-primary" href="{{ route('cotizaciones.create', $s->id_solicitud) }}" title="Crear cotización">
                    <i class="bi bi-file-earmark-plus"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('cotizaciones.comparar', $s->id_solicitud) }}" title="Comparar cotizaciones">
                    <i class="bi bi-diagram-3"></i>
                  </a>
                </div>
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
        <p class="mt-3 text-muted">No hay solicitudes pendientes de cotización</p>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Volver al Dashboard</a>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
  // Activar tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
</script>
@endpush
@endsection
