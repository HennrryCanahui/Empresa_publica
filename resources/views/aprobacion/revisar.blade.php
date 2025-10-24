@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-stamp me-2"></i>Revisar Solicitud para Aprobación</h2>
    <a href="{{ route('aprobacion.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver a Pendientes
    </a>
</div>
@endsection

@section('content')

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Errores en el formulario:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Información de Prioridad y Estado -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert @if($solicitud->prioridad == 'Urgente') alert-danger @elseif($solicitud->prioridad == 'Alta') alert-warning @else alert-info @endif d-flex align-items-center">
            <i class="bi bi-info-circle-fill display-6 me-3"></i>
            <div>
                <h5 class="mb-1">
                    Solicitud: <strong>{{ $solicitud->numero_solicitud }}</strong> 
                    - Prioridad: 
                    @if($solicitud->prioridad == 'Urgente')
                        <span class="badge bg-danger">URGENTE</span>
                    @elseif($solicitud->prioridad == 'Alta')
                        <span class="badge bg-warning text-dark">ALTA</span>
                    @else
                        <span class="badge bg-info">{{ $solicitud->prioridad }}</span>
                    @endif
                </h5>
                <p class="mb-0">Creada el {{ $solicitud->fecha_creacion->format('d/m/Y') }} por {{ $solicitud->usuarioCreador->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <!-- Columna Izquierda: Información de la Solicitud -->
  <div class="col-lg-8">
    
    <!-- Información General -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white">
        <i class="bi bi-file-text me-2"></i>Información General de la Solicitud
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <p class="mb-1"><i class="bi bi-building text-primary me-2"></i><strong>Unidad Solicitante:</strong></p>
            <p class="ms-4 text-muted">{{ $solicitud->unidadSolicitante->nombre ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6 mb-3">
            <p class="mb-1"><i class="bi bi-person text-primary me-2"></i><strong>Solicitado por:</strong></p>
            <p class="ms-4 text-muted">{{ $solicitud->usuarioCreador->name ?? 'N/A' }}</p>
          </div>
          <div class="col-12 mb-2">
            <p class="mb-1"><i class="bi bi-chat-left-text text-primary me-2"></i><strong>Descripción:</strong></p>
            <p class="ms-4">{{ $solicitud->descripcion }}</p>
          </div>
          <div class="col-12">
            <p class="mb-1"><i class="bi bi-journal-text text-primary me-2"></i><strong>Justificación:</strong></p>
            <p class="ms-4">{{ $solicitud->justificacion }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Validación Presupuestaria -->
    @if($solicitud->presupuesto)
    <div class="card mb-3 border-success">
      <div class="card-header bg-success text-white">
        <i class="bi bi-calculator me-2"></i>Validación Presupuestaria
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="mb-2"><i class="bi bi-cash-stack text-success me-2"></i><strong>Monto Estimado:</strong></p>
            <p class="ms-4 fs-5 text-success fw-bold">Q {{ number_format($solicitud->presupuesto->monto_estimado, 2) }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><i class="bi bi-bookmark text-info me-2"></i><strong>Partida Presupuestaria:</strong></p>
            <p class="ms-4"><span class="badge bg-info">{{ $solicitud->presupuesto->partida_presupuestaria }}</span></p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><i class="bi bi-piggy-bank text-warning me-2"></i><strong>Disponibilidad:</strong></p>
            <p class="ms-4">Q {{ number_format($solicitud->presupuesto->disponibilidad_actual, 2) }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><i class="bi bi-person-badge text-secondary me-2"></i><strong>Validado por:</strong></p>
            <p class="ms-4">{{ $solicitud->presupuesto->usuarioPresupuesto->name ?? 'N/A' }}</p>
          </div>
          @if($solicitud->presupuesto->observaciones)
          <div class="col-12">
            <p class="mb-2"><i class="bi bi-chat-square-text text-muted me-2"></i><strong>Observaciones:</strong></p>
            <div class="alert alert-light ms-4">{{ $solicitud->presupuesto->observaciones }}</div>
          </div>
          @endif
        </div>
      </div>
    </div>
    @endif

    <!-- Cotización Seleccionada -->
    @if($solicitud->cotizaciones->count())
    <div class="card mb-3">
      <div class="card-header bg-white">
        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Cotización Seleccionada
      </div>
      <div class="card-body">
        @php $c = $solicitud->cotizaciones->first(); @endphp
        @if($c)
          <div class="row mb-3">
            <div class="col-md-6">
              <p class="mb-1"><i class="bi bi-building text-primary me-2"></i><strong>Proveedor:</strong></p>
              <p class="ms-4">{{ $c->proveedor->razon_social ?? 'N/A' }}</p>
              @if($c->proveedor->telefono)
                <p class="ms-4 text-muted"><i class="bi bi-telephone me-1"></i>{{ $c->proveedor->telefono }}</p>
              @endif
            </div>
            <div class="col-md-6">
              <p class="mb-1"><i class="bi bi-calendar-check text-success me-2"></i><strong>Fecha de Cotización:</strong></p>
              <p class="ms-4">{{ $c->fecha_cotizacion->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-1"><i class="bi bi-cash text-success me-2"></i><strong>Monto Total:</strong></p>
              <p class="ms-4 fs-4 fw-bold text-success">Q {{ number_format($c->monto_total, 2) }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-1"><i class="bi bi-hourglass text-warning me-2"></i><strong>Tiempo de Entrega:</strong></p>
              <p class="ms-4">{{ $c->tiempo_entrega ?? 'No especificado' }}</p>
            </div>
          </div>

          @if($c->detalles->count())
          <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-list-ul me-2"></i>Detalle de Productos</h6>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                @foreach($c->detalles as $index => $d)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $d->producto->nombre ?? 'N/A' }}</td>
                    <td class="text-center"><strong>{{ $d->cantidad }}</strong></td>
                    <td class="text-end">Q {{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="text-end fw-bold text-success">Q {{ number_format($d->precio_total, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                  <td class="text-end fw-bold text-success fs-5">Q {{ number_format($c->monto_total, 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
          @endif

          @if($c->observaciones)
          <div class="mt-3">
            <p class="mb-1"><i class="bi bi-chat-quote text-muted me-2"></i><strong>Observaciones del Proveedor:</strong></p>
            <div class="alert alert-light">{{ $c->observaciones }}</div>
          </div>
          @endif
        @endif
      </div>
    </div>
    @endif

    <!-- Historial de Estados -->
    @if($solicitud->historialEstados->count())
    <div class="card">
      <div class="card-header bg-white">
        <i class="bi bi-clock-history me-2"></i>Historial de Estados
      </div>
      <div class="card-body">
        <div class="timeline">
          @foreach($solicitud->historialEstados->sortByDesc('fecha_cambio') as $h)
            <div class="mb-3 pb-3 border-bottom">
              <div class="d-flex justify-content-between">
                <span class="badge bg-secondary">{{ str_replace('_', ' ', $h->estado_nuevo) }}</span>
                <small class="text-muted">{{ $h->fecha_cambio->format('d/m/Y H:i') }}</small>
              </div>
              @if($h->observaciones)
                <p class="mb-0 mt-2 text-muted"><small>{{ $h->observaciones }}</small></p>
              @endif
              <small class="text-muted">Por: {{ $h->usuario->name ?? 'Sistema' }}</small>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif

  </div>

  <!-- Columna Derecha: Formulario de Decisión -->
  <div class="col-lg-4">
    <form method="POST" action="{{ route('aprobacion.procesar', $solicitud->id_solicitud) }}" class="card sticky-top" style="top: 20px;" id="formDecision">
      @csrf
      <div class="card-header bg-warning text-dark">
        <i class="bi bi-stamp me-2"></i><strong>Registrar Decisión de Aprobación</strong>
      </div>
      <div class="card-body">
        
        <div class="mb-3">
          <label class="form-label fw-bold"><i class="bi bi-check-square me-1"></i>Decisión <span class="text-danger">*</span></label>
          <select name="decision" class="form-select @error('decision') is-invalid @enderror" required id="selectDecision">
            <option value="">-- Seleccione --</option>
            <option value="Aprobada">✓ Aprobar</option>
            <option value="Rechazada">✗ Rechazar</option>
            <option value="Requiere_Revision">⚠ Requiere Revisión</option>
          </select>
          @error('decision')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Alert dinámico según decisión -->
        <div id="alertDecision" class="d-none mb-3"></div>

        <div class="mb-3">
          <label class="form-label fw-bold"><i class="bi bi-currency-dollar me-1"></i>Monto Aprobado</label>
          <input type="number" 
                 name="monto_aprobado" 
                 step="0.01" 
                 min="0" 
                 class="form-control @error('monto_aprobado') is-invalid @enderror" 
                 value="{{ old('monto_aprobado', $solicitud->cotizaciones->first()->monto_total ?? $solicitud->monto_total_estimado) }}"
                 placeholder="0.00">
          <small class="text-muted">Monto de cotización: Q {{ number_format($solicitud->cotizaciones->first()->monto_total ?? $solicitud->monto_total_estimado, 2) }}</small>
          @error('monto_aprobado')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold"><i class="bi bi-chat-left-text me-1"></i>Observaciones <span class="text-danger">*</span></label>
          <textarea name="observaciones" 
                    rows="4" 
                    class="form-control @error('observaciones') is-invalid @enderror" 
                    required 
                    placeholder="Indique las razones de su decisión..."
                    minlength="10">{{ old('observaciones') }}</textarea>
          <small class="text-muted">Mínimo 10 caracteres</small>
          @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold"><i class="bi bi-list-check me-1"></i>Condiciones de Aprobación</label>
          <textarea name="condiciones_aprobacion" 
                    rows="3" 
                    class="form-control @error('condiciones_aprobacion') is-invalid @enderror" 
                    placeholder="Condiciones especiales, restricciones o requisitos adicionales (opcional)">{{ old('condiciones_aprobacion') }}</textarea>
          @error('condiciones_aprobacion')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

      </div>
      <div class="card-footer bg-white">
        <div class="d-grid gap-2">
          <button class="btn btn-success btn-lg" type="submit">
            <i class="bi bi-check2-circle me-1"></i>Registrar Decisión
          </button>
          <a href="{{ route('aprobacion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i>Cancelar
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // Cambiar alert dinámico según decisión
  document.getElementById('selectDecision').addEventListener('change', function() {
    const alertDiv = document.getElementById('alertDecision');
    const decision = this.value;
    
    alertDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
    
    if (decision === 'Aprobada') {
      alertDiv.classList.add('alert', 'alert-success');
      alertDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><strong>Aprobar:</strong> La solicitud será enviada a Adquisiciones para proceder con la compra.';
    } else if (decision === 'Rechazada') {
      alertDiv.classList.add('alert', 'alert-danger');
      alertDiv.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i><strong>Rechazar:</strong> La solicitud será marcada como rechazada y no continuará el proceso.';
    } else if (decision === 'Requiere_Revision') {
      alertDiv.classList.add('alert', 'alert-warning');
      alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Requiere Revisión:</strong> La solicitud será devuelta para correcciones o aclaraciones adicionales.';
    } else {
      alertDiv.classList.add('d-none');
    }
  });
</script>
@endpush
@endsection