@extends('layouts.app')

@section('title', 'Nueva Cotización')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-receipt"></i> 
            Nueva Cotización - Solicitud #{{ $solicitud->numero_solicitud }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<form action="{{ route('cotizaciones.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id_solicitud" value="{{ $solicitud->id_solicitud }}">

    <div class="row">
        <div class="col-md-8">
            <!-- Información de la Cotización -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información de la Cotización</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero_cotizacion" class="form-label">Número de Cotización *</label>
                            <input type="text" class="form-control @error('numero_cotizacion') is-invalid @enderror" 
                                   id="numero_cotizacion" name="numero_cotizacion" 
                                   value="{{ old('numero_cotizacion') }}" required>
                            @error('numero_cotizacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="id_proveedor" class="form-label">Proveedor *</label>
                            <select class="form-select @error('id_proveedor') is-invalid @enderror" 
                                    id="id_proveedor" name="id_proveedor" required>
                                <option value="">Seleccione...</option>
                                @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id_proveedor }}" 
                                        {{ old('id_proveedor') == $proveedor->id_proveedor ? 'selected' : '' }}>
                                    {{ $proveedor->razon_social }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_proveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_cotizacion" class="form-label">Fecha de Cotización *</label>
                            <input type="date" class="form-control @error('fecha_cotizacion') is-invalid @enderror" 
                                   id="fecha_cotizacion" name="fecha_cotizacion" 
                                   value="{{ old('fecha_cotizacion', date('Y-m-d')) }}" required>
                            @error('fecha_cotizacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="tiempo_entrega" class="form-label">Tiempo de Entrega *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('tiempo_entrega') is-invalid @enderror" 
                                       id="tiempo_entrega" name="tiempo_entrega" 
                                       value="{{ old('tiempo_entrega') }}" min="1" required>
                                <span class="input-group-text">días</span>
                                @error('tiempo_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="condiciones_pago" class="form-label">Condiciones de Pago *</label>
                            <textarea class="form-control @error('condiciones_pago') is-invalid @enderror" 
                                      id="condiciones_pago" name="condiciones_pago" rows="2" required>{{ old('condiciones_pago') }}</textarea>
                            @error('condiciones_pago')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detalle de Productos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitud->detalles as $detalle)
                                <tr>
                                    <td>
                                        {{ $detalle->producto->nombre }}
                                        @if($detalle->especificaciones_adicionales)
                                        <br>
                                        <small class="text-muted">
                                            {{ $detalle->especificaciones_adicionales }}
                                        </small>
                                        @endif
                                        <input type="hidden" name="productos[]" value="{{ $detalle->id_producto }}">
                                    </td>
                                    <td>
                                        {{ number_format($detalle->cantidad, 2) }}
                                        {{ $detalle->producto->unidad_medida }}
                                        <input type="hidden" name="cantidades[]" value="{{ $detalle->cantidad }}">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control precio-input @error('precios.'.$loop->index) is-invalid @enderror" 
                                               name="precios[]" 
                                               value="{{ old('precios.'.$loop->index, $detalle->precio_estimado_unitario) }}" 
                                               min="0" step="0.01" required>
                                        @error('precios.'.$loop->index)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <span class="total-span">0.00</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong id="granTotal">0.00</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Documento de Cotización -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Documento de Cotización</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="archivo_cotizacion" class="form-label">Adjuntar PDF *</label>
                        <input type="file" class="form-control @error('archivo_cotizacion') is-invalid @enderror" 
                               id="archivo_cotizacion" name="archivo_cotizacion" 
                               accept=".pdf" required>
                        <div class="form-text">Formato PDF (Máx. 5MB)</div>
                        @error('archivo_cotizacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="draft" class="btn btn-secondary">
                            <i class="bi bi-save"></i> Guardar como Borrador
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Registrar Cotización
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function calcularTotales() {
        let granTotal = 0;
        document.querySelectorAll('tr').forEach(row => {
            const cantidad = parseFloat(row.querySelector('input[name="cantidades[]"]')?.value) || 0;
            const precio = parseFloat(row.querySelector('.precio-input')?.value) || 0;
            const total = cantidad * precio;
            const totalSpan = row.querySelector('.total-span');
            if (totalSpan) {
                totalSpan.textContent = total.toFixed(2);
                granTotal += total;
            }
        });
        document.getElementById('granTotal').textContent = granTotal.toFixed(2);
    }

    // Calcular totales cuando cambian los precios
    document.addEventListener('input', function(e) {
        if (e.target.matches('.precio-input')) {
            calcularTotales();
        }
    });

    // Calcular totales iniciales
    calcularTotales();
});
</script>
@endpush
@endsection
