@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-file-earmark-text me-2"></i>Detalle de Aprobación</h2>
    <div>
        <a href="{{ route('aprobacion.historial') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-clock-history me-1"></i>Historial
        </a>
        <a href="{{ route('aprobacion.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>
@endsection

@section('content')

<!-- Badge de Estado de Decisión -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert @if($aprobacion->decision == 'Aprobada') alert-success @elseif($aprobacion->decision == 'Rechazada') alert-danger @else alert-warning @endif d-flex align-items-center" role="alert">
            @if($aprobacion->decision == 'Aprobada')
                <i class="bi bi-check-circle-fill display-6 me-3"></i>
            @elseif($aprobacion->decision == 'Rechazada')
                <i class="bi bi-x-circle-fill display-6 me-3"></i>
            @else
                <i class="bi bi-exclamation-triangle-fill display-6 me-3"></i>
            @endif
            <div>
                <h5 class="mb-1">Estado: {{ str_replace('_', ' ', $aprobacion->decision) }}</h5>
                <p class="mb-0">Decisión tomada el {{ $aprobacion->fecha_aprobacion->format('d/m/Y') }} a las {{ $aprobacion->fecha_aprobacion->format('H:i') }} por {{ $aprobacion->usuarioAutoridad->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <!-- Información de la Aprobación -->
    <div class="card mb-3 border-primary">
      <div class="card-header bg-primary text-white">
        <i class="bi bi-stamp me-2"></i>Información de la Decisión
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5"><i class="bi bi-file-text text-primary me-1"></i>Solicitud:</dt>
          <dd class="col-sm-7">
            <strong>{{ $aprobacion->solicitud->numero_solicitud ?? 'N/A' }}</strong><br>
            <small class="text-muted">{{ $aprobacion->solicitud->unidadSolicitante->nombre ?? '' }}</small>
          </dd>
          
          <dt class="col-sm-5"><i class="bi bi-check-square text-success me-1"></i>Decisión:</dt>
          <dd class="col-sm-7">
            @if($aprobacion->decision == 'Aprobada')
              <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aprobada</span>
            @elseif($aprobacion->decision == 'Rechazada')
              <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rechazada</span>
            @elseif($aprobacion->decision == 'Requiere_Revision')
              <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Requiere Revisión</span>
            @else
              <span class="badge bg-secondary">{{ str_replace('_', ' ', $aprobacion->decision) }}</span>
            @endif
          </dd>
          
          <dt class="col-sm-5"><i class="bi bi-cash-stack text-success me-1"></i>Monto Aprobado:</dt>
          <dd class="col-sm-7 fs-4 fw-bold text-success">Q {{ number_format($aprobacion->monto_aprobado ?? 0, 2) }}</dd>
          
          <dt class="col-sm-5"><i class="bi bi-calendar-check text-info me-1"></i>Fecha Aprobación:</dt>
          <dd class="col-sm-7">{{ $aprobacion->fecha_aprobacion->format('d/m/Y H:i') }}</dd>
          
          <dt class="col-sm-5"><i class="bi bi-person-badge text-warning me-1"></i>Autoridad:</dt>
          <dd class="col-sm-7">{{ $aprobacion->usuarioAutoridad->name ?? 'N/A' }}</dd>
        </dl>
      </div>
    </div>

    <!-- Información de la Solicitud -->
    <div class="card mb-3">
      <div class="card-header bg-white">
        <i class="bi bi-info-circle me-2"></i>Información de la Solicitud
      </div>
      <div class="card-body">
        <p class="mb-2"><strong><i class="bi bi-person me-2 text-muted"></i>Solicitante:</strong> {{ $aprobacion->solicitud->usuarioCreador->name ?? 'N/A' }}</p>
        <p class="mb-2"><strong><i class="bi bi-calendar3 me-2 text-muted"></i>Fecha Creación:</strong> {{ $aprobacion->solicitud->fecha_creacion->format('d/m/Y') }}</p>
        <p class="mb-2"><strong><i class="bi bi-speedometer2 me-2 text-muted"></i>Prioridad:</strong> 
          @if($aprobacion->solicitud->prioridad == 'Urgente')
            <span class="badge bg-danger">Urgente</span>
          @elseif($aprobacion->solicitud->prioridad == 'Alta')
            <span class="badge bg-warning text-dark">Alta</span>
          @else
            <span class="badge bg-info">{{ $aprobacion->solicitud->prioridad }}</span>
          @endif
        </p>
        <p class="mb-0"><strong><i class="bi bi-cash me-2 text-muted"></i>Monto Estimado:</strong> Q {{ number_format($aprobacion->solicitud->monto_total_estimado ?? 0, 2) }}</p>
      </div>
    </div>

    <!-- Presupuesto -->
    @if($aprobacion->solicitud->presupuesto)
    <div class="card mb-3 border-success">
      <div class="card-header bg-success text-white">
        <i class="bi bi-calculator me-2"></i>Validación Presupuestaria
      </div>
      <div class="card-body">
        <p class="mb-2"><strong><i class="bi bi-cash-stack text-success me-2"></i>Monto Validado:</strong> Q {{ number_format($aprobacion->solicitud->presupuesto->monto_estimado, 2) }}</p>
        <p class="mb-2"><strong><i class="bi bi-bookmark text-info me-2"></i>Partida:</strong> <span class="badge bg-info">{{ $aprobacion->solicitud->presupuesto->partida_presupuestaria }}</span></p>
        <p class="mb-0"><strong><i class="bi bi-person text-muted me-2"></i>Validado por:</strong> {{ $aprobacion->solicitud->presupuesto->usuarioPresupuesto->name ?? 'N/A' }}</p>
      </div>
    </div>
    @endif
  </div>

  <div class="col-lg-6">
    <!-- Observaciones y Condiciones -->
    <div class="card mb-3">
      <div class="card-header bg-white">
        <i class="bi bi-chat-square-text me-2"></i>Observaciones y Condiciones
      </div>
      <div class="card-body">
        <div class="mb-3">
          <h6 class="text-primary"><i class="bi bi-chat-left-quote me-1"></i>Observaciones:</h6>
          <div class="alert alert-light border ms-3">
            <p class="mb-0">{{ $aprobacion->observaciones ?? 'Sin observaciones' }}</p>
          </div>
        </div>
        
        @if($aprobacion->condiciones_aprobacion)
        <div>
          <h6 class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Condiciones de Aprobación:</h6>
          <div class="alert alert-warning ms-3">
            <p class="mb-0">{{ $aprobacion->condiciones_aprobacion }}</p>
          </div>
        </div>
        @endif
      </div>
    </div>

    <!-- Cotización Seleccionada -->
    @if($aprobacion->solicitud->cotizaciones->count())
    <div class="card mb-3">
      <div class="card-header bg-white">
        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Cotización Seleccionada
      </div>
      <div class="card-body">
        @php $c = $aprobacion->solicitud->cotizaciones->first(); @endphp
        <p class="mb-2"><strong><i class="bi bi-building text-primary me-2"></i>Proveedor:</strong> {{ $c->proveedor->razon_social ?? 'N/A' }}</p>
        <p class="mb-2"><strong><i class="bi bi-cash text-success me-2"></i>Monto Total:</strong> <span class="fs-5 fw-bold text-success">Q {{ number_format($c->monto_total, 2) }}</span></p>
        <p class="mb-0"><strong><i class="bi bi-calendar-check text-info me-2"></i>Fecha Cotización:</strong> {{ $c->fecha_cotizacion->format('d/m/Y') }}</p>
      </div>
    </div>
    @endif
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
      <p class="ms-3">{{ $aprobacion->solicitud->descripcion ?? 'Sin descripción' }}</p>
    </div>
    <div>
      <h6 class="text-primary"><i class="bi bi-justify-left me-1"></i>Justificación:</h6>
      <p class="ms-3 mb-0">{{ $aprobacion->solicitud->justificacion ?? 'Sin justificación' }}</p>
    </div>
  </div>
</div>

<!-- Detalles de Productos -->
<div class="card">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <span><i class="bi bi-box-seam me-2"></i>Productos Solicitados</span>
      <span class="badge bg-primary">{{ $aprobacion->solicitud->detalles->count() }} productos</span>
    </div>
  </div>
  <div class="card-body p-0">
    @if($aprobacion->solicitud->detalles->count())
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
            @foreach($aprobacion->solicitud->detalles as $index => $d)
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
              <td class="text-end fw-bold text-success fs-5">Q {{ number_format($aprobacion->solicitud->monto_total_estimado ?? 0, 2) }}</td>
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
      <a href="{{ route('aprobacion.historial') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>Ver Historial
      </a>
      <a href="{{ route('aprobacion.index') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left me-1"></i>Volver a Pendientes
      </a>
    </div>
  </div>
</div>
@endsection