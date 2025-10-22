@extends('layouts.app')

@section('title', isset($solicitud) ? 'Editar Solicitud' : 'Nueva Solicitud')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-file-text"></i> 
            {{ isset($solicitud) ? 'Editar Solicitud' : 'Nueva Solicitud' }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<form action="{{ isset($solicitud) ? route('solicitudes.update', $solicitud) : route('solicitudes.store') }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    @if(isset($solicitud))
        @method('PUT')
    @endif

    <div class="row">
        <!-- Información General -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción de la Solicitud *</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion', $solicitud->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="prioridad" class="form-label">Prioridad *</label>
                            <select class="form-select @error('prioridad') is-invalid @enderror" 
                                    id="prioridad" name="prioridad" required>
                                <option value="">Seleccione...</option>
                                    @foreach(['Baja' => 'Baja', 'Media' => 'Media', 'Alta' => 'Alta', 'Urgente' => 'Urgente'] as $key => $value)
                                    <option value="{{ $key }}" 
                                            {{ old('prioridad', $solicitud->prioridad ?? '') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                            </select>
                            @error('prioridad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                <label for="fecha_limite" class="form-label">Fecha Requerida</label>
                <input type="date" class="form-control @error('fecha_limite') is-invalid @enderror" 
                    id="fecha_limite" name="fecha_limite" 
                    value="{{ old('fecha_limite', isset($solicitud) && $solicitud->fecha_limite ? $solicitud->fecha_limite->format('Y-m-d') : '') }}"
                    min="{{ date('Y-m-d') }}">
                @error('fecha_limite')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="justificacion" class="form-label">Justificación *</label>
                            <textarea class="form-control @error('justificacion') is-invalid @enderror" 
                                      id="justificacion" name="justificacion" rows="3" required>{{ old('justificacion', $solicitud->justificacion ?? '') }}</textarea>
                            @error('justificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Productos Solicitados</h5>
                    <button type="button" class="btn btn-primary btn-sm" id="btnAgregarProducto">
                        <i class="bi bi-plus-circle"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Est.</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($solicitud))
                                    @foreach($solicitud->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <select name="productos[]" class="form-select producto-select" required>
                                                <option value="">Seleccione...</option>
                                                @foreach($productos as $producto)
                                                <option value="{{ $producto->id_producto }}"
                                                        data-precio="{{ $producto->precio_referencia }}"
                                                        {{ $detalle->id_producto == $producto->id_producto ? 'selected' : '' }}>
                                                    {{ $producto->codigo }} - {{ $producto->nombre }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <textarea name="especificaciones[]" class="form-control mt-2" 
                                                      placeholder="Especificaciones adicionales">{{ $detalle->especificaciones_adicionales }}</textarea>
                                        </td>
                                        <td>
                          <input type="number" name="cantidades[]" 
                                                   class="form-control cantidad-input" 
                                                   value="{{ $detalle->cantidad }}" min="1" step="0.01" required>
                                        </td>
                                        <td>
                              <input type="number" name="precios[]" 
                                                   class="form-control precio-input" 
                                                   value="{{ $detalle->precio_estimado_unitario }}" 
                                                   min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <span class="total-span">{{ number_format($detalle->precio_estimado_total, 2) }}</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Estimado:</strong></td>
                                    <td><strong id="granTotal">0.00</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Documentos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Documentos de Soporte</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="documentos" class="form-label">Adjuntar Documentos</label>
                        <input type="file" class="form-control @error('documentos.*') is-invalid @enderror" 
                               id="documentos" name="documentos[]" multiple>
                        <div class="form-text">Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX (Máx. 5MB por archivo)</div>
                        @error('documentos.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(isset($solicitud) && $solicitud->documentos->count() > 0)
                    <div class="list-group mt-3">
                        @foreach($solicitud->documentos as $documento)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('documentos.download', $documento) }}">
                                <i class="bi bi-file-earmark"></i> {{ $documento->nombre_archivo }}
                            </a>
                            <form action="{{ route('documentos.destroy', $documento) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este documento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif
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
                            <i class="bi bi-send"></i> Enviar Solicitud
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
    // Template para nueva fila de producto
    function getProductoTemplate() {
        return `
            <tr>
                <td>
                    <select name="productos[]" class="form-select producto-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->id_producto }}"
                                data-precio="{{ $producto->precio_referencia }}">
                            {{ $producto->codigo }} - {{ $producto->nombre }}
                        </option>
                        @endforeach
                    </select>
                    <textarea name="especificaciones[]" class="form-control mt-2" 
                              placeholder="Especificaciones adicionales"></textarea>
                </td>
                <td>
                    <input type="number" name="cantidades[]" 
                           class="form-control cantidad-input" 
                           value="1" min="1" step="0.01" required>
                </td>
                <td>
                    <input type="number" name="precios[]" 
                           class="form-control precio-input" 
                           value="0" min="0" step="0.01" required>
                </td>
                <td>
                    <span class="total-span">0.00</span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // Agregar producto
    document.getElementById('btnAgregarProducto').addEventListener('click', function() {
        const tbody = document.querySelector('#tablaProductos tbody');
        tbody.insertAdjacentHTML('beforeend', getProductoTemplate());
    });

    // Eliminar producto
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar')) {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                e.target.closest('tr').remove();
                calcularTotales();
            }
        }
    });

    // Calcular total por fila y gran total
    function calcularTotales() {
        let granTotal = 0;
        document.querySelectorAll('#tablaProductos tbody tr').forEach(row => {
            const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
            const total = cantidad * precio;
            row.querySelector('.total-span').textContent = total.toFixed(2);
            granTotal += total;
        });
        document.getElementById('granTotal').textContent = granTotal.toFixed(2);
    }

    // Eventos para recalcular
    document.addEventListener('input', function(e) {
        if (e.target.matches('.cantidad-input') || e.target.matches('.precio-input')) {
            calcularTotales();
        }
    });

    // Actualizar precio al seleccionar producto
    document.addEventListener('change', function(e) {
        if (e.target.matches('.producto-select')) {
            const option = e.target.options[e.target.selectedIndex];
            const precio = option.dataset.precio || 0;
            const row = e.target.closest('tr');
            row.querySelector('.precio-input').value = precio;
            calcularTotales();
        }
    });

    // Calcular totales iniciales
    calcularTotales();
});
</script>
@endpush
@endsection
