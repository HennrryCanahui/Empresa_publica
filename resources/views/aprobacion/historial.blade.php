@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Aprobaciones</h2>
    <a href="{{ route('aprobacion.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i>Volver a Pendientes
    </a>
</div>
@endsection

@section('content')

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="bi bi-check-circle display-4 text-success"></i>
                <h3 class="mt-2">{{ $aprobaciones->where('decision', 'Aprobada')->count() }}</h3>
                <p class="text-muted mb-0">Aprobadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <i class="bi bi-x-circle display-4 text-danger"></i>
                <h3 class="mt-2">{{ $aprobaciones->where('decision', 'Rechazada')->count() }}</h3>
                <p class="text-muted mb-0">Rechazadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle display-4 text-warning"></i>
                <h3 class="mt-2">{{ $aprobaciones->where('decision', 'Requiere_Revision')->count() }}</h3>
                <p class="text-muted mb-0">Requieren Revisión</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="bi bi-list-check display-4 text-primary"></i>
                <h3 class="mt-2">{{ $aprobaciones->total() }}</h3>
                <p class="text-muted mb-0">Total Decisiones</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-table me-2"></i>Todas las Decisiones de Aprobación</h5>
      <span class="badge bg-secondary">{{ $aprobaciones->total() }} registros</span>
    </div>
  </div>
  <div class="card-body p-0">
    @if($aprobaciones->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th>Solicitud</th>
              <th>Unidad</th>
              <th>Solicitante</th>
              <th>Decisión</th>
              <th>Monto Aprobado</th>
              <th>Autoridad</th>
              <th>Observaciones</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($aprobaciones as $a)
              <tr>
                <td>
                  <small>{{ $a->fecha_aprobacion->format('d/m/Y') }}</small><br>
                  <small class="text-muted">{{ $a->fecha_aprobacion->format('H:i') }}</small>
                </td>
                <td>
                  <strong class="text-primary">{{ $a->solicitud->numero_solicitud ?? 'N/A' }}</strong><br>
                  <small class="text-muted">{{ Str::limit($a->solicitud->descripcion ?? '', 40) }}</small>
                </td>
                <td>
                  <small>{{ $a->solicitud->unidadSolicitante->nombre ?? 'N/A' }}</small>
                </td>
                <td>
                  <small>{{ trim(($a->solicitud->usuarioCreador->nombre ?? '') . ' ' . ($a->solicitud->usuarioCreador->apellido ?? '')) ?: 'N/A' }}</small>
                </td>
                <td>
                  @if($a->decision == 'Aprobada')
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aprobada</span>
                  @elseif($a->decision == 'Rechazada')
                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rechazada</span>
                  @elseif($a->decision == 'Requiere_Revision')
                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Requiere Revisión</span>
                  @else
                    <span class="badge bg-secondary">{{ str_replace('_', ' ', $a->decision) }}</span>
                  @endif
                </td>
                <td class="fw-bold text-success">Q {{ number_format($a->monto_aprobado ?? 0, 2) }}</td>
                <td>
                  <small>{{ trim(($a->usuarioAutoridad->nombre ?? '') . ' ' . ($a->usuarioAutoridad->apellido ?? '')) ?: 'N/A' }}</small>
                </td>
                <td>
                  <small data-bs-toggle="tooltip" title="{{ $a->observaciones }}">
                    {{ Str::limit($a->observaciones, 50) }}
                  </small>
                </td>
                <td class="text-center">
                  <a href="{{ route('aprobacion.ver', $a->id_aprobacion) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      @if($aprobaciones->hasPages())
      <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-muted">
            Mostrando {{ $aprobaciones->firstItem() }} - {{ $aprobaciones->lastItem() }} de {{ $aprobaciones->total() }} aprobaciones
          </div>
          <div>
            {{ $aprobaciones->links() }}
          </div>
        </div>
      </div>
      @endif
    @else
      <div class="p-5 text-center">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted">No hay aprobaciones registradas</p>
        <a href="{{ route('aprobacion.index') }}" class="btn btn-outline-primary">Ir a Pendientes</a>
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
