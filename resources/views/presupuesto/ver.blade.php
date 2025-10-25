@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Detalle de Validación Presupuestaria</h2>
    <div>
        <a href="{{ route('presupuesto.historial') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-clock-history me-1"></i>Historial
        </a>
        <a href="{{ route('presupuesto.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>
@endsection

@section('content')

<!-- Badge de Estado de Validación -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert @if($presupuesto->validacion == 'Válido') alert-success @elseif($presupuesto->validacion == 'Rechazado') alert-danger @else alert-warning @endif d-flex align-items-center" role="alert">
            @if($presupuesto->validacion == 'Válido')
                <i class="bi bi-check-circle-fill display-6 me-3"></i>
            @elseif($presupuesto->validacion == 'Rechazado')
                <i class="bi bi-x-circle-fill display-6 me-3"></i>
            @else
                <i class="bi bi-exclamation-triangle-fill display-6 me-3"></i>
            @endif
            <div>
                <h5 class="mb-1">Estado: {{ str_replace('_', ' ', $presupuesto->validacion) }}</h5>
                <p class="mb-0">Validación presupuestaria procesada el {{ $presupuesto->fecha_revision->format('d/m/Y') }} a las {{ $presupuesto->fecha_revision->format('H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card mb-3 border-primary">
      <div class="card-header bg-primary text-white">
        <i class="bi bi-calculator me-2"></i>Información Presupuestaria
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5"><i class="bi bi-file-text text-primary me-1"></i>Solicitud:</dt>
          <dd class="col-sm-7">
            <strong>{{ $presupuesto->solicitud->numero_solicitud ?? 'N/A' }}</strong><br>
            <small class="text-muted">{{ $presupuesto->solicitud->unidadSolicitante->nombre_unidad ?? '' }}</small>
          </dd>
          
          <dt class="col-sm-5"><i class="bi bi-cash-stack text-success me-1"></i>Monto Estimado:</dt>
          <dd class="col-sm-7 fw-bold text-success">Q {{ number_format($presupuesto->monto_estimado, 2) }}</dd>
          
          <dt class="col-sm-5"><i class="bi bi-piggy-bank text-info me-1"></i>Disponibilidad:</dt>
          <dd class="col-sm-7 text-info">Q {{ number_format($presupuesto->disponibilidad_actual, 2) }}</dd>
          
          <dt class="col-sm-5"><i class="bi bi-bookmark text-warning me-1"></i>Partida:</dt>
          <dd class="col-sm-7"><span class="badge bg-info">{{ $presupuesto->partida_presupuestaria }}</span></dd>
          
          <dt class="col-sm-5"><i class="bi bi-calendar-check text-secondary me-1"></i>Fecha Revisión:</dt>
          <dd class="col-sm-7">{{ $presupuesto->fecha_revision->format('d/m/Y H:i') }}</dd>
          
          <dt class="col-sm-5"><i class="bi bi-person-badge text-primary me-1"></i>Validado por:</dt>
          <dd class="col-sm-7">{{ trim(($presupuesto->usuarioPresupuesto->nombre ?? '') . ' ' . ($presupuesto->usuarioPresupuesto->apellido ?? '')) ?: 'N/A' }}</dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card mb-3">
      <div class="card-header bg-white">
        <i class="bi bi-chat-square-text me-2"></i>Observaciones y Comentarios
      </div>
      <div class="card-body">
        @if($presupuesto->observaciones)
          <div class="alert alert-light border">
            <i class="bi bi-quote text-muted"></i>
            <p class="mb-0 ms-3">{{ $presupuesto->observaciones }}</p>
          </div>
        @else
          <p class="text-muted fst-italic mb-0">Sin observaciones registradas</p>
        @endif
      </div>
    </div>

    <!-- Info adicional de la solicitud -->
    <div class="card mb-3">
      <div class="card-header bg-white">
        <i class="bi bi-info-circle me-2"></i>Información de la Solicitud
      </div>
      <div class="card-body">
        <p class="mb-2"><strong><i class="bi bi-calendar3 me-2 text-muted"></i>Fecha Solicitud:</strong> {{ $presupuesto->solicitud->fecha_creacion->format('d/m/Y') }}</p>
        <p class="mb-2"><strong><i class="bi bi-speedometer2 me-2 text-muted"></i>Prioridad:</strong> 
          @if($presupuesto->solicitud->prioridad == 'Urgente')
            <span class="badge bg-danger">Urgente</span>
          @elseif($presupuesto->solicitud->prioridad == 'Alta')
            <span class="badge bg-warning text-dark">Alta</span>
          @elseif($presupuesto->solicitud->prioridad == 'Media')
            <span class="badge bg-info">Media</span>
          @else
            <span class="badge bg-secondary">Baja</span>
          @endif
        </p>
  <p class="mb-0"><strong><i class="bi bi-person me-2 text-muted"></i>Solicitante:</strong> {{ trim(($presupuesto->solicitud->usuarioCreador->nombre ?? '') . ' ' . ($presupuesto->solicitud->usuarioCreador->apellido ?? '')) ?: 'N/A' }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Descripción y Justificación -->
<div class="card mb-3">
  <div class="card-header bg-white">
    <i class="bi bi-file-text me-2"></i>Descripción y Justificación de la Solicitud
  </div>
  <div class="card-body">
    <div class="mb-3">
      <h6 class="text-primary"><i class="bi bi-info-circle me-1"></i>Descripción:</h6>
      <p class="ms-3">{{ $presupuesto->solicitud->descripcion ?? 'Sin descripción' }}</p>
    </div>
    <div>
      <h6 class="text-primary"><i class="bi bi-justify-left me-1"></i>Justificación:</h6>
      <p class="ms-3 mb-0">{{ $presupuesto->solicitud->justificacion ?? 'Sin justificación' }}</p>
    </div>
  </div>
</div>

<!-- Detalles de Productos -->
<div class="card">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <span><i class="bi bi-box-seam me-2"></i>Productos Solicitados</span>
      <span class="badge bg-primary">{{ $presupuesto->solicitud->detalles->count() }} productos</span>
    </div>
  </div>
  <div class="card-body p-0">
    @if($presupuesto->solicitud->detalles->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Producto/Servicio</th>
              <th>Categoría</th>
              <th>Especificaciones</th>
              <th>Cantidad</th>
              <th class="text-end">Precio Unit.</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($presupuesto->solicitud->detalles as $index => $d)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                  <strong>{{ $d->producto->nombre ?? 'N/A' }}</strong><br>
                  <small class="text-muted">{{ $d->producto->descripcion ?? '' }}</small>
                </td>
                <td>
                  <span class="badge bg-secondary">{{ $d->producto->categoria->nombre ?? 'N/A' }}</span>
                </td>
                <td>
                  <small>{{ Str::limit($d->especificaciones_tecnicas, 60) }}</small>
                </td>
                <td>
                  <strong>{{ $d->cantidad }}</strong> {{ $d->unidad->abreviatura ?? '' }}
                </td>
                <td class="text-end">Q {{ number_format($d->precio_estimado, 2) }}</td>
                <td class="text-end fw-bold text-success">Q {{ number_format($d->subtotal_estimado, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot class="table-light">
            <tr>
              <td colspan="6" class="text-end fw-bold">TOTAL ESTIMADO:</td>
              <td class="text-end fw-bold text-success fs-5">Q {{ number_format($presupuesto->solicitud->monto_total_estimado ?? 0, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    @else
      <div class="p-4 text-center text-muted">
        <i class="bi bi-inbox display-4"></i>
        <p class="mt-2">No hay productos asociados a esta solicitud</p>
      </div>
    @endif
  </div>
  <div class="card-footer bg-white">
    <div class="d-flex justify-content-between">
      <a href="{{ route('presupuesto.historial') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Ver Historial
      </a>
      <a href="{{ route('presupuesto.index') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left me-1"></i>Volver a Pendientes
      </a>
    </div>
  </div>
</div>
@endsection