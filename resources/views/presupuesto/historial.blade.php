@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Validaciones</h2>
    <a href="{{ route('presupuesto.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i>Volver a Pendientes
    </a>
</div>
@endsection

@section('content')

<!-- Estadísticas del Historial -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="bi bi-check-circle display-4 text-success"></i>
                <h3 class="mt-2">{{ $validaciones->where('validacion', 'Válido')->count() }}</h3>
                <p class="text-muted mb-0">Aprobadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle display-4 text-warning"></i>
                <h3 class="mt-2">{{ $validaciones->where('validacion', 'Requiere_Ajuste')->count() }}</h3>
                <p class="text-muted mb-0">Requieren Ajuste</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <i class="bi bi-x-circle display-4 text-danger"></i>
                <h3 class="mt-2">{{ $validaciones->where('validacion', 'Rechazado')->count() }}</h3>
                <p class="text-muted mb-0">Rechazadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="bi bi-list-check display-4 text-primary"></i>
                <h3 class="mt-2">{{ $validaciones->total() }}</h3>
                <p class="text-muted mb-0">Total Validaciones</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-table me-2"></i>Todas las Validaciones Presupuestarias</h5>
      <span class="badge bg-secondary">{{ $validaciones->total() }} registros</span>
    </div>
  </div>
  <div class="card-body p-0">
    @if($validaciones->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th>Solicitud</th>
              <th>Unidad</th>
              <th>Partida</th>
              <th>Monto</th>
              <th>Disponibilidad</th>
              <th>Resultado</th>
              <th>Validado por</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($validaciones as $p)
            <tr>
              <td>
                <small>{{ $p->fecha_revision->format('d/m/Y') }}</small><br>
                <small class="text-muted">{{ $p->fecha_revision->format('H:i') }}</small>
              </td>
              <td>
                <strong>{{ $p->solicitud->numero_solicitud ?? 'N/A' }}</strong><br>
                <small class="text-muted">{{ Str::limit($p->solicitud->descripcion ?? '', 40) }}</small>
              </td>
              <td>
                <small>{{ $p->solicitud->unidadSolicitante->nombre_unidad ?? 'N/A' }}</small>
              </td>
              <td>
                <span class="badge bg-info">{{ $p->partida_presupuestaria }}</span>
              </td>
              <td class="fw-bold">Q {{ number_format($p->monto_estimado, 2) }}</td>
              <td>
                <small>Q {{ number_format($p->disponibilidad_actual, 2) }}</small>
              </td>
              <td>
                @if($p->validacion == 'Válido')
                  <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Válido</span>
                @elseif($p->validacion == 'Rechazado')
                  <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rechazado</span>
                @elseif($p->validacion == 'Requiere_Ajuste')
                  <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Requiere Ajuste</span>
                @else
                  <span class="badge bg-secondary">{{ str_replace('_', ' ', $p->validacion) }}</span>
                @endif
              </td>
              <td>
                <small>{{ trim(($p->usuarioPresupuesto->nombre ?? '') . ' ' . ($p->usuarioPresupuesto->apellido ?? '')) ?: 'N/A' }}</small>
              </td>
              <td class="text-center">
                <a href="{{ route('presupuesto.ver', $p->id_presupuesto) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      @if($validaciones->hasPages())
      <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-muted">
            Mostrando {{ $validaciones->firstItem() }} - {{ $validaciones->lastItem() }} de {{ $validaciones->total() }} validaciones
          </div>
          <div>
            {{ $validaciones->links() }}
          </div>
        </div>
      </div>
      @endif
    @else
      <div class="p-5 text-center">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted">No hay validaciones presupuestarias registradas</p>
        <a href="{{ route('presupuesto.index') }}" class="btn btn-outline-primary">Ir a Pendientes</a>
      </div>
    @endif
  </div>
</div>
@endsection