@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
  <h2 class="h4 mb-0"><i class="bi bi-diagram-3 me-2"></i>Comparar Cotizaciones</h2>
  <div>
    <a href="{{ route('cotizaciones.create', $solicitud->id_solicitud) }}" class="btn btn-outline-primary me-2">
      <i class="bi bi-plus-circle me-1"></i>Nueva Cotización
    </a>
    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
  </div>
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

<!-- Información de la Solicitud -->
<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-file-text me-2"></i>Solicitud: <strong>{{ $solicitud->numero_solicitud }}</strong>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <p class="mb-2"><strong><i class="bi bi-building text-primary me-2"></i>Unidad:</strong> {{ $solicitud->unidadSolicitante->nombre ?? 'N/A' }}</p>
        <p class="mb-2"><strong><i class="bi bi-person text-primary me-2"></i>Solicitante:</strong> {{ trim(($solicitud->usuarioCreador->nombre ?? '') . ' ' . ($solicitud->usuarioCreador->apellido ?? '')) ?: 'N/A' }}</p>
        <p class="mb-0"><strong><i class="bi bi-calendar3 text-primary me-2"></i>Fecha:</strong> {{ $solicitud->fecha_creacion->format('d/m/Y') }}</p>
      </div>
      <div class="col-md-6">
        <p class="mb-2"><strong><i class="bi bi-chat-left-text text-primary me-2"></i>Descripción:</strong> {{ $solicitud->descripcion }}</p>
        <p class="mb-2"><strong><i class="bi bi-cash text-success me-2"></i>Monto Estimado:</strong> Q {{ number_format($solicitud->monto_total_estimado ?? 0, 2) }}</p>
        <p class="mb-0"><strong><i class="bi bi-speedometer2 text-warning me-2"></i>Prioridad:</strong>
          @if($solicitud->prioridad == 'Urgente')
            <span class="badge bg-danger">Urgente</span>
          @elseif($solicitud->prioridad == 'Alta')
            <span class="badge bg-warning text-dark">Alta</span>
          @elseif($solicitud->prioridad == 'Media')
            <span class="badge bg-info">Media</span>
          @else
            <span class="badge bg-secondary">Baja</span>
          @endif
        </p>
      </div>
    </div>
  </div>
  </div>

@if($solicitud->cotizaciones->count())

  <!-- Resumen Comparativo -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-center border-info">
        <div class="card-body">
          <i class="bi bi-file-earmark-text display-4 text-info"></i>
          <h3 class="mt-2">{{ $solicitud->cotizaciones->count() }}</h3>
          <p class="text-muted mb-0">Cotizaciones</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center border-success">
        <div class="card-body">
          <i class="bi bi-cash-stack display-4 text-success"></i>
          <h3 class="mt-2">Q {{ number_format($solicitud->cotizaciones->min('monto_total'), 2) }}</h3>
          <p class="text-muted mb-0">Menor Precio</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center border-warning">
        <div class="card-body">
          <i class="bi bi-clock display-4 text-warning"></i>
          <h3 class="mt-2">{{ $solicitud->cotizaciones->min('tiempo_entrega_dias') ?? 'N/A' }}</h3>
          <p class="text-muted mb-0">Menor Tiempo (días)</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center border-primary">
        <div class="card-body">
          <i class="bi bi-check-circle display-4 text-primary"></i>
          <h3 class="mt-2">{{ $solicitud->cotizaciones->where('estado', 'Seleccionada')->count() ? '1' : '0' }}</h3>
          <p class="text-muted mb-0">Seleccionada</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Cotizaciones -->
  @foreach($solicitud->cotizaciones->sortBy('monto_total') as $index => $c)
    <div class="card mb-3 @if($c->estado === 'Seleccionada') border-success border-3 @elseif($index === 0) border-primary @endif">
      <div class="card-header @if($c->estado === 'Seleccionada') bg-success text-white @else bg-white @endif">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h5 class="mb-0">
              @if($index === 0 && $c->estado !== 'Seleccionada')
                <i class="bi bi-award text-warning me-2" title="Mejor precio"></i>
              @endif
              @if($c->estado === 'Seleccionada')
                <i class="bi bi-check-circle-fill me-2"></i>
              @endif
              {{ $c->numero_cotizacion }}
            </h5>
            <div class="@if($c->estado === 'Seleccionada') text-white @else text-muted @endif">
              <i class="bi bi-building me-1"></i>{{ $c->proveedor->razon_social ?? 'N/A' }}
            </div>
          </div>
          <div class="col-md-6 text-end">
            @if($c->estado === 'Seleccionada')
              <span class="badge bg-light text-success fs-6"><i class="bi bi-check-circle me-1"></i>SELECCIONADA</span>
            @elseif($c->estado === 'Activa')
              <span class="badge bg-info fs-6">ACTIVA</span>
            @else
              <span class="badge bg-secondary fs-6">{{ strtoupper($c->estado) }}</span>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-2">
            <small class="text-muted d-block"><i class="bi bi-calendar-event me-1"></i>Fecha</small>
            <div class="fw-bold">{{ $c->fecha_cotizacion ? \Carbon\Carbon::parse($c->fecha_cotizacion)->format('d/m/Y') : 'N/A' }}</div>
          </div>
          <div class="col-md-2">
            <small class="text-muted d-block"><i class="bi bi-calendar-check me-1"></i>Válida hasta</small>
            <div>{{ $c->fecha_validez ? \Carbon\Carbon::parse($c->fecha_validez)->format('d/m/Y') : 'No especificado' }}</div>
          </div>
          <div class="col-md-2">
            <small class="text-muted d-block"><i class="bi bi-clock-history me-1"></i>Entrega</small>
            <div class="fw-bold">{{ $c->tiempo_entrega_dias ?? 'N/A' }} días</div>
          </div>
          <div class="col-md-3">
            <small class="text-muted d-block"><i class="bi bi-credit-card me-1"></i>Condiciones de pago</small>
            <div>{{ $c->condiciones_pago ?? 'No especificado' }}</div>
          </div>
          <div class="col-md-3 text-end">
            <small class="text-muted d-block">TOTAL</small>
            <h4 class="mb-0 @if($c->estado === 'Seleccionada') text-success @else text-primary @endif">
              Q {{ number_format($c->monto_total, 2) }}
            </h4>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio Unitario</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($c->detalles as $i => $d)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>
                    <strong>{{ $d->producto->nombre ?? 'N/A' }}</strong><br>
                    <small class="text-muted">{{ $d->producto->codigo ?? '' }}</small>
                  </td>
                  <td class="text-center">{{ $d->cantidad }}</td>
                  <td class="text-end">Q {{ number_format($d->precio_unitario, 2) }}</td>
                  <td class="text-end fw-bold text-success">Q {{ number_format($d->precio_total, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot class="table-light">
              <tr>
                <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                <td class="text-end fw-bold fs-5 @if($c->estado === 'Seleccionada') text-success @else text-primary @endif">
                  Q {{ number_format($c->monto_total, 2) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        @if($c->observaciones)
          <div class="alert alert-light border mt-3">
            <strong><i class="bi bi-chat-quote me-1"></i>Observaciones:</strong><br>
            {{ $c->observaciones }}
          </div>
        @endif

        @if($c->estado === 'Activa' && in_array(Auth::user()->rol, ['Compras', 'Admin']))
          <div class="mt-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSeleccionar{{ $c->id_cotizacion }}">
              <i class="bi bi-check2-circle me-1"></i>Seleccionar esta Cotización
            </button>
            <a href="{{ route('cotizaciones.ver', $c->id_cotizacion) }}" class="btn btn-outline-primary">
              <i class="bi bi-eye me-1"></i>Ver Detalle
            </a>
          </div>

          <div class="modal fade" id="modalSeleccionar{{ $c->id_cotizacion }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ route('cotizaciones.seleccionar', $c->id_cotizacion) }}" method="POST">
                  @csrf
                  <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Seleccionar Cotización</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p><strong>¿Está seguro de seleccionar esta cotización?</strong></p>
                    <p class="mb-3">Proveedor: <strong>{{ $c->proveedor->razon_social ?? '' }}</strong></p>
                    <p class="mb-3">Monto Total: <strong class="text-success">Q {{ number_format($c->monto_total, 2) }}</strong></p>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Justificación de selección: <span class="text-danger">*</span></label>
                      <textarea name="justificacion" class="form-control" rows="4" required placeholder="Indique las razones por las cuales selecciona esta cotización (precio, calidad, tiempo de entrega, etc.)"></textarea>
                      <small class="text-muted">Mínimo 20 caracteres</small>
                    </div>

                    <div class="alert alert-warning">
                      <i class="bi bi-exclamation-triangle me-2"></i>
                      <strong>Importante:</strong> Las demás cotizaciones serán marcadas como descartadas.
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                      <i class="bi bi-check2-circle me-1"></i>Confirmar Selección
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @elseif($c->estado === 'Seleccionada')
          <div class="mt-3">
            <a href="{{ route('cotizaciones.ver', $c->id_cotizacion) }}" class="btn btn-outline-success">
              <i class="bi bi-eye me-1"></i>Ver Detalle Completo
            </a>
          </div>
        @endif
      </div>
    </div>
  @endforeach

  @if($solicitud->cotizaciones->where('estado', 'Seleccionada')->count() > 0 && in_array(Auth::user()->rol, ['Compras', 'Admin']))
    <div class="card border-primary">
      <div class="card-body text-center">
        <h5 class="mb-3"><i class="bi bi-check-circle text-success me-2"></i> Cotización seleccionada correctamente</h5>
        <form action="{{ route('cotizaciones.enviar-aprobacion', $solicitud->id_solicitud) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-send me-1"></i>Enviar a Aprobación de Autoridad
          </button>
        </form>
      </div>
    </div>
  @endif

@else
  <div class="card">
    <div class="card-body text-center p-5">
      <i class="bi bi-inbox display-1 text-muted"></i>
      <h4 class="mt-3 text-muted">No hay cotizaciones registradas</h4>
      <p class="text-muted">Aún no se han registrado cotizaciones para esta solicitud.</p>
      <a href="{{ route('cotizaciones.create', $solicitud->id_solicitud) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Crear Primera Cotización
      </a>
    </div>
  </div>
@endif

@endsection
