@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-clipboard-check me-2"></i>Validar Presupuesto</h2>
    <a href="{{ route('presupuesto.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-7">
    <!-- Información de la Solicitud -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-text me-2"></i><strong>{{ $solicitud->numero_solicitud }}</strong></span>
        <span class="badge bg-light text-primary">{{ $solicitud->estado }}</span>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="mb-2"><strong><i class="bi bi-building me-1"></i>Unidad:</strong><br>
            <span class="ms-3">{{ $solicitud->unidadSolicitante->nombre ?? 'N/A' }}</span></p>
            <p class="mb-2"><strong><i class="bi bi-person me-1"></i>Solicitante:</strong><br>
            <span class="ms-3">{{ $solicitud->usuarioCreador->name ?? 'N/A' }}</span></p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><strong><i class="bi bi-calendar me-1"></i>Fecha:</strong><br>
            <span class="ms-3">{{ \Carbon\Carbon::parse($solicitud->fecha_creacion)->format('d/m/Y H:i') }}</span></p>
            <p class="mb-2"><strong><i class="bi bi-flag me-1"></i>Prioridad:</strong><br>
            <span class="ms-3">
              @if($solicitud->prioridad == 'Urgente')
                <span class="badge bg-danger">{{ $solicitud->prioridad }}</span>
              @elseif($solicitud->prioridad == 'Alta')
                <span class="badge bg-warning text-dark">{{ $solicitud->prioridad }}</span>
              @elseif($solicitud->prioridad == 'Media')
                <span class="badge bg-info">{{ $solicitud->prioridad }}</span>
              @else
                <span class="badge bg-secondary">{{ $solicitud->prioridad }}</span>
              @endif
            </span></p>
          </div>
        </div>
        
        <hr>
        
        <p class="mb-2"><strong><i class="bi bi-card-text me-1"></i>Descripción:</strong></p>
        <p class="ms-3 text-muted">{{ $solicitud->descripcion }}</p>
        
        <p class="mb-2"><strong><i class="bi bi-journal-text me-1"></i>Justificación:</strong></p>
        <p class="ms-3 text-muted">{{ $solicitud->justificacion }}</p>
        
        <div class="alert alert-info d-flex align-items-center mt-3">
          <i class="bi bi-currency-dollar fs-3 me-3"></i>
          <div>
            <strong>Monto Total Estimado:</strong><br>
            <span class="fs-4">Q {{ number_format($solicitud->monto_total_estimado ?? 0, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Detalle de Productos -->
    <div class="card">
      <div class="card-header bg-white">
        <i class="bi bi-list-ul me-2"></i><strong>Productos Solicitados</strong>
        <span class="badge bg-primary ms-2">{{ $solicitud->detalles->count() }} items</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Especificaciones</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($solicitud->detalles as $index => $d)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $d->producto->nombre ?? 'N/A' }}</strong></td>
                <td><small class="text-muted">{{ $d->especificaciones_tecnicas ?? 'Sin especificaciones' }}</small></td>
                <td class="text-center">{{ $d->cantidad }} {{ $d->unidad->abreviatura ?? '' }}</td>
                <td class="text-end">Q {{ number_format($d->precio_estimado ?? 0, 2) }}</td>
                <td class="text-end fw-bold">Q {{ number_format($d->subtotal_estimado ?? 0, 2) }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot class="table-light">
              <tr>
                <th colspan="5" class="text-end">TOTAL GENERAL:</th>
                <th class="text-end text-primary fs-5">Q {{ number_format($solicitud->monto_total_estimado ?? 0, 2) }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulario de Validación -->
  <div class="col-lg-5">
    <form method="POST" action="{{ route('presupuesto.procesar', $solicitud->id_solicitud) }}" class="card sticky-top" style="top: 20px;">
      @csrf
      <div class="card-header bg-success text-white">
        <i class="bi bi-check-circle me-2"></i>Formulario de Validación Presupuestaria
      </div>
      <div class="card-body">
        
        <div class="alert alert-warning">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <small>Complete todos los campos para validar la disponibilidad presupuestaria.</small>
        </div>

        <div class="mb-3">
          <label class="form-label"><i class="bi bi-cash me-1"></i>Monto Estimado <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text">Q</span>
            <input name="monto_estimado" type="number" step="0.01" min="0" class="form-control @error('monto_estimado') is-invalid @enderror" 
                   value="{{ old('monto_estimado', $solicitud->monto_total_estimado) }}" required>
          </div>
          @error('monto_estimado')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
          <small class="text-muted">Monto que se asignará según disponibilidad</small>
        </div>

        <div class="mb-3">
          <label class="form-label"><i class="bi bi-tag me-1"></i>Partida Presupuestaria <span class="text-danger">*</span></label>
          <input name="partida_presupuestaria" type="text" class="form-control @error('partida_presupuestaria') is-invalid @enderror" 
                 placeholder="Ej: 0-11-01-000-00-1441" value="{{ old('partida_presupuestaria') }}" required>
          @error('partida_presupuestaria')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
          <small class="text-muted">Código de la partida presupuestaria</small>
        </div>

        <div class="mb-3">
          <label class="form-label"><i class="bi bi-wallet2 me-1"></i>Disponibilidad Actual <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text">Q</span>
            <input name="disponibilidad_actual" type="number" step="0.01" min="0" class="form-control @error('disponibilidad_actual') is-invalid @enderror" 
                   value="{{ old('disponibilidad_actual') }}" required>
          </div>
          @error('disponibilidad_actual')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
          <small class="text-muted">Saldo disponible en la partida</small>
        </div>

        <hr>

        <div class="mb-3">
          <label class="form-label"><i class="bi bi-clipboard-check me-1"></i>Resultado de Validación <span class="text-danger">*</span></label>
          <select name="validacion" class="form-select @error('validacion') is-invalid @enderror" required id="validacionSelect">
            <option value="">-- Seleccione una opción --</option>
            <option value="Válido" {{ old('validacion') == 'Válido' ? 'selected' : '' }}>✓ Válido - Continuar a Compras</option>
            <option value="Requiere_Ajuste" {{ old('validacion') == 'Requiere_Ajuste' ? 'selected' : '' }}>⚠ Requiere Ajuste</option>
            <option value="Rechazado" {{ old('validacion') == 'Rechazado' ? 'selected' : '' }}>✗ Rechazado</option>
          </select>
          @error('validacion')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label"><i class="bi bi-chat-left-text me-1"></i>Observaciones</label>
          <textarea name="observaciones" rows="4" class="form-control @error('observaciones') is-invalid @enderror" 
                    placeholder="Ingrese comentarios o detalles adicionales sobre la validación...">{{ old('observaciones') }}</textarea>
          @error('observaciones')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
          <small class="text-muted">Detalles adicionales sobre la validación (opcional)</small>
        </div>

        <div id="alertaValidacion" class="alert" style="display:none;" role="alert"></div>
      </div>
      <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('presupuesto.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle me-1"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-success btn-lg">
          <i class="bi bi-check2-circle me-1"></i>Procesar Validación
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const validacionSelect = document.getElementById('validacionSelect');
    const alertaDiv = document.getElementById('alertaValidacion');
    
    if (validacionSelect) {
        validacionSelect.addEventListener('change', function() {
            const valor = this.value;
            alertaDiv.style.display = 'block';
            
            switch(valor) {
                case 'Válido':
                    alertaDiv.className = 'alert alert-success';
                    alertaDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i><strong>Aprobado:</strong> La solicitud será enviada al departamento de Compras para cotización.';
                    break;
                case 'Requiere_Ajuste':
                    alertaDiv.className = 'alert alert-warning';
                    alertaDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i><strong>Requiere Ajuste:</strong> La solicitud volverá al solicitante para modificaciones.';
                    break;
                case 'Rechazado':
                    alertaDiv.className = 'alert alert-danger';
                    alertaDiv.innerHTML = '<i class="bi bi-x-circle me-2"></i><strong>Rechazado:</strong> La solicitud será rechazada. No se procederá con la compra.';
                    break;
                default:
                    alertaDiv.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
@endsection