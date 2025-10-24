@extends('layouts.app')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0"><i class="bi bi-receipt me-2"></i>Nueva Cotización</h2>
    <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Cancelar
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

<!-- Información de la Solicitud -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-center">
            <i class="bi bi-info-circle-fill display-6 me-3"></i>
            <div>
                <h5 class="mb-1">Solicitud: <strong>{{ $solicitud->numero_solicitud }}</strong></h5>
                <p class="mb-0">{{ $solicitud->descripcion }}</p>
                <p class="mb-0"><small><strong>Unidad:</strong> {{ $solicitud->unidadSolicitante->nombre ?? 'N/A' }} | <strong>Monto Estimado:</strong> Q {{ number_format($solicitud->monto_total_estimado ?? 0, 2) }}</small></p>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('cotizaciones.store') }}" id="formCotizacion">
  @csrf
  <input type="hidden" name="id_solicitud" value="{{ $solicitud->id_solicitud }}">
  
  <div class="row">
    <!-- Formulario Principal -->
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header bg-primary text-white">
          <i class="bi bi-file-earmark-text me-2"></i>Datos de la Cotización
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold"><i class="bi bi-building me-1"></i>Proveedor <span class="text-danger">*</span></label>
              <select name="id_proveedor" class="form-select @error('id_proveedor') is-invalid @enderror" required>
                <option value="">-- Seleccione un proveedor --</option>
                @foreach($proveedores as $p)
                  <option value="{{ $p->id_proveedor }}" {{ old('id_proveedor') == $p->id_proveedor ? 'selected' : '' }}>
                    {{ $p->codigo }} - {{ $p->razon_social }}
                    @if($p->telefono) ({{ $p->telefono }}) @endif
                  </option>
                @endforeach
              </select>
              @error('id_proveedor')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold"><i class="bi bi-calendar-event me-1"></i>Fecha de Cotización <span class="text-danger">*</span></label>
              <input type="date" 
                     name="fecha_cotizacion" 
                     class="form-control @error('fecha_cotizacion') is-invalid @enderror" 
                     value="{{ old('fecha_cotizacion', date('Y-m-d')) }}" 
                     required>
              @error('fecha_cotizacion')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold"><i class="bi bi-calendar-check me-1"></i>Válida Hasta</label>
              <input type="date" 
                     name="fecha_validez" 
                     class="form-control @error('fecha_validez') is-invalid @enderror" 
                     value="{{ old('fecha_validez') }}">
              @error('fecha_validez')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold"><i class="bi bi-clock me-1"></i>Tiempo de Entrega (días)</label>
              <input type="number" 
                     name="tiempo_entrega_dias" 
                     min="1" 
                     class="form-control @error('tiempo_entrega_dias') is-invalid @enderror" 
                     value="{{ old('tiempo_entrega_dias') }}"
                     placeholder="Ej: 15">
              @error('tiempo_entrega_dias')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold"><i class="bi bi-credit-card me-1"></i>Condiciones de Pago</label>
            <input type="text" 
                   name="condiciones_pago" 
                   class="form-control @error('condiciones_pago') is-invalid @enderror" 
                   value="{{ old('condiciones_pago') }}"
                   placeholder="Ej: 50% anticipo, 50% contra entrega">
            @error('condiciones_pago')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold"><i class="bi bi-chat-left-text me-1"></i>Observaciones</label>
            <textarea name="observaciones" 
                      rows="3" 
                      class="form-control @error('observaciones') is-invalid @enderror" 
                      placeholder="Detalles adicionales, garantías, especificaciones...">{{ old('observaciones') }}</textarea>
            @error('observaciones')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <!-- Tabla de Productos -->
      <div class="card">
        <div class="card-header bg-white">
          <i class="bi bi-box-seam me-2"></i>Productos a Cotizar
          <span class="badge bg-primary ms-2">{{ $solicitud->detalles->count() }} productos</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Producto</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-end">Precio Unitario <span class="text-danger">*</span></th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach($solicitud->detalles as $i => $d)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>
                    <strong>{{ $d->producto->nombre ?? 'N/A' }}</strong><br>
                    <small class="text-muted">Código: {{ $d->producto->codigo ?? 'N/A' }}</small>
                    <input type="hidden" name="productos[{{ $i }}][id_producto]" value="{{ $d->id_producto }}">
                    <input type="hidden" name="productos[{{ $i }}][cantidad]" value="{{ $d->cantidad }}" class="producto-cantidad">
                  </td>
                  <td class="text-center">
                    <span class="badge bg-info">{{ $d->cantidad }} {{ $d->unidad->abreviatura ?? '' }}</span>
                  </td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-text">Q</span>
                      <input type="number" 
                             name="productos[{{ $i }}][precio_unitario]" 
                             class="form-control text-end producto-precio @error('productos.'.$i.'.precio_unitario') is-invalid @enderror" 
                             step="0.01" 
                             min="0" 
                             value="{{ old('productos.'.$i.'.precio_unitario', 0) }}"
                             required
                             data-index="{{ $i }}">
                      @error('productos.'.$i.'.precio_unitario')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </td>
                  <td class="text-end">
                    <strong class="text-success producto-subtotal" data-index="{{ $i }}">Q 0.00</strong>
                  </td>
                </tr>
                @endforeach
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <td colspan="4" class="text-end fw-bold fs-5">TOTAL COTIZACIÓN:</td>
                  <td class="text-end fw-bold text-success fs-4" id="totalGeneral">Q 0.00</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel Lateral: Resumen -->
    <div class="col-lg-4">
      <div class="card sticky-top" style="top: 20px;">
        <div class="card-header bg-success text-white">
          <i class="bi bi-calculator me-2"></i>Resumen de Cotización
        </div>
        <div class="card-body">
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Monto Estimado Solicitud:</small>
            <h4 class="text-muted">Q {{ number_format($solicitud->monto_total_estimado ?? 0, 2) }}</h4>
          </div>
          
          <hr>
          
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Total Cotización:</small>
            <h3 class="text-success mb-0" id="totalResumen">Q 0.00</h3>
          </div>

          <div class="alert alert-warning mb-3">
            <small><i class="bi bi-info-circle me-1"></i>Complete todos los precios unitarios para calcular el total</small>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-lg">
              <i class="bi bi-save me-1"></i>Guardar Cotización
            </button>
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-x-circle me-1"></i>Cancelar
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para calcular subtotales y total
    function calcularTotales() {
        let totalGeneral = 0;
        
        document.querySelectorAll('.producto-precio').forEach(function(inputPrecio) {
            const index = inputPrecio.dataset.index;
            const cantidad = parseFloat(document.querySelector(`input[name="productos[${index}][cantidad]"]`).value) || 0;
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = cantidad * precio;
            
            // Actualizar subtotal en la tabla
            const subtotalElement = document.querySelector(`.producto-subtotal[data-index="${index}"]`);
            if (subtotalElement) {
                subtotalElement.textContent = 'Q ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            totalGeneral += subtotal;
        });
        
        // Actualizar total general
        const totalFormateado = 'Q ' + totalGeneral.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        document.getElementById('totalGeneral').textContent = totalFormateado;
        document.getElementById('totalResumen').textContent = totalFormateado;
    }
    
    // Escuchar cambios en precios
    document.querySelectorAll('.producto-precio').forEach(function(input) {
        input.addEventListener('input', calcularTotales);
        input.addEventListener('change', calcularTotales);
    });
    
    // Calcular totales al cargar
    calcularTotales();
});
</script>
@endpush
@endsection