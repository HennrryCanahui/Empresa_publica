@extends('layouts.app')

@section('header')
    <h2 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Solicitud</h2>
@endsection

@section('content')
<form action="{{ route('solicitudes.update', $solicitud->id_solicitud) }}" method="POST" id="form-solicitud">
    @csrf
    @method('PUT')
    
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>Información General
                <span class="badge bg-dark ms-2">{{ $solicitud->numero_solicitud }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6>Por favor corrija los siguientes errores:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_unidad_solicitante" class="form-label">Unidad Solicitante <span class="text-danger">*</span></label>
                    <select name="id_unidad_solicitante" id="id_unidad_solicitante" 
                            class="form-select @error('id_unidad_solicitante') is-invalid @enderror" required>
                        <option value="">Seleccione una unidad...</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id_unidad }}" 
                                {{ old('id_unidad_solicitante', $solicitud->id_unidad_solicitante) == $unidad->id_unidad ? 'selected' : '' }}>
                                {{ $unidad->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_unidad_solicitante')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                    <select name="prioridad" id="prioridad" 
                            class="form-select @error('prioridad') is-invalid @enderror" required>
                        <option value="Baja" {{ old('prioridad', $solicitud->prioridad) == 'Baja' ? 'selected' : '' }}>Baja</option>
                        <option value="Media" {{ old('prioridad', $solicitud->prioridad) == 'Media' ? 'selected' : '' }}>Media</option>
                        <option value="Alta" {{ old('prioridad', $solicitud->prioridad) == 'Alta' ? 'selected' : '' }}>Alta</option>
                        <option value="Urgente" {{ old('prioridad', $solicitud->prioridad) == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                    @error('prioridad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="fecha_limite" class="form-label">Fecha Límite</label>
                    <input type="date" name="fecha_limite" id="fecha_limite" 
                           class="form-control @error('fecha_limite') is-invalid @enderror"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           value="{{ old('fecha_limite', $solicitud->fecha_limite ? \Carbon\Carbon::parse($solicitud->fecha_limite)->format('Y-m-d') : '') }}">
                    @error('fecha_limite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                <textarea name="descripcion" id="descripcion" rows="3"
                          class="form-control @error('descripcion') is-invalid @enderror"
                          placeholder="Describe detalladamente lo que necesitas..." required>{{ old('descripcion', $solicitud->descripcion) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="justificacion" class="form-label">Justificación <span class="text-danger">*</span></label>
                <textarea name="justificacion" id="justificacion" rows="3"
                          class="form-control @error('justificacion') is-invalid @enderror"
                          placeholder="Explica por qué es necesaria esta solicitud..." required>{{ old('justificacion', $solicitud->justificacion) }}</textarea>
                @error('justificacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Productos/Servicios</h5>
            <button type="button" class="btn btn-light btn-sm" id="btn-agregar-producto">
                <i class="bi bi-plus-circle me-1"></i>Agregar Producto
            </button>
        </div>
        <div class="card-body">
            <div id="productos-container">
                <!-- Los productos existentes se cargarán aquí -->
            </div>

            <div class="text-end mt-3">
                <h5>Total Estimado: <span id="total-general" class="text-primary">Q 0.00</span></h5>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('solicitudes.show', $solicitud->id_solicitud) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-warning btn-lg">
                    <i class="bi bi-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</form>

<script>
// Datos de productos desde el servidor
const productosData = {!! json_encode($productos->map(function($p) {
    return [
        'id' => $p->id_producto,
        'codigo' => $p->codigo,
        'nombre' => $p->nombre,
        'precio' => $p->precio_referencia ?? 0,
        'unidad' => $p->unidad_medida ?? ''
    ];
})) !!};

// Productos existentes de la solicitud
const productosExistentes = {!! json_encode($solicitud->detalles->map(function($d) {
    return [
        'id_producto' => $d->id_producto,
        'cantidad' => $d->cantidad,
        'precio_unitario' => $d->precio_estimado_unitario ?? 0,
        'precio_total' => $d->precio_estimado_total ?? 0,
        'unidad' => $d->producto->unidad_medida ?? '',
        'especificaciones' => $d->especificaciones_adicionales ?? ''
    ];
})) !!};

let productoIndex = 0;

// Función para crear un nuevo item de producto
function crearProductoHTML(index, datosExistentes = null) {
    let optionsHTML = '<option value="">Seleccione un producto...</option>';
    
    productosData.forEach(function(producto) {
        const selected = datosExistentes && datosExistentes.id_producto == producto.id ? 'selected' : '';
        optionsHTML += '<option value="' + producto.id + '" ' +
                       'data-precio="' + producto.precio + '" ' +
                       'data-unidad="' + producto.unidad + '" ' +
                       selected + '>' +
                       producto.codigo + ' - ' + producto.nombre +
                       '</option>';
    });

    const cantidad = datosExistentes ? datosExistentes.cantidad : '';
    const precioRef = datosExistentes ? 'Q ' + parseFloat(datosExistentes.precio_unitario).toFixed(2) : '';
    const subtotal = datosExistentes ? 'Q ' + parseFloat(datosExistentes.precio_total).toFixed(2) : '';
    const unidad = datosExistentes ? datosExistentes.unidad : '';
    const especificaciones = datosExistentes ? datosExistentes.especificaciones : '';

    return `
        <div class="producto-item border rounded p-3 mb-3" data-index="${index}">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Producto/Servicio <span class="text-danger">*</span></label>
                    <select name="productos[${index}][id_producto]" 
                            class="form-select producto-select" 
                            required>
                        ${optionsHTML}
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="productos[${index}][cantidad]" 
                           class="form-control cantidad-input"
                           step="0.01" 
                           min="0.01" 
                           placeholder="0.00"
                           value="${cantidad}"
                           required>
                </div>
                <div class="col-md-1 mb-2">
                    <label class="form-label">Unidad</label>
                    <input type="text" class="form-control unidad-medida" value="${unidad}" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Precio Ref.</label>
                    <input type="text" class="form-control precio-referencia" value="${precioRef}" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Subtotal Est.</label>
                    <input type="text" class="form-control subtotal-estimado" value="${subtotal}" readonly>
                </div>
                <div class="col-md-1 mb-2">
                    <button type="button" class="btn btn-danger btn-sm w-100 btn-eliminar-producto">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label">Especificaciones Adicionales</label>
                    <textarea name="productos[${index}][especificaciones_adicionales]" 
                              class="form-control" 
                              rows="2"
                              placeholder="Detalles adicionales, características específicas, etc.">${especificaciones}</textarea>
                </div>
            </div>
        </div>
    `;
}

// Cargar productos existentes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('productos-container');
    
    if (productosExistentes.length > 0) {
        productosExistentes.forEach(function(producto) {
            const productoHTML = crearProductoHTML(productoIndex, producto);
            container.insertAdjacentHTML('beforeend', productoHTML);
            productoIndex++;
        });
    } else {
        // Si no hay productos, agregar uno vacío
        agregarProducto();
    }
    
    actualizarBotonesEliminar();
    calcularTotalGeneral();
});

// Función para agregar producto
function agregarProducto() {
    const container = document.getElementById('productos-container');
    const nuevoProductoHTML = crearProductoHTML(productoIndex);
    container.insertAdjacentHTML('beforeend', nuevoProductoHTML);
    productoIndex++;
    actualizarBotonesEliminar();
}

// Event listener para el botón agregar
document.getElementById('btn-agregar-producto').addEventListener('click', function() {
    console.log('Agregando producto...');
    agregarProducto();
});

// Delegar eventos para selects de producto y cantidades
document.getElementById('productos-container').addEventListener('change', function(e) {
    if (e.target.classList.contains('producto-select')) {
        const productoItem = e.target.closest('.producto-item');
        const option = e.target.options[e.target.selectedIndex];
        const precio = parseFloat(option.getAttribute('data-precio')) || 0;
        const unidad = option.getAttribute('data-unidad') || '';
        
        productoItem.querySelector('.precio-referencia').value = 'Q ' + precio.toFixed(2);
        productoItem.querySelector('.unidad-medida').value = unidad;
        
        calcularSubtotal(productoItem);
    }
    
    if (e.target.classList.contains('cantidad-input')) {
        const productoItem = e.target.closest('.producto-item');
        calcularSubtotal(productoItem);
    }
});

// Calcular subtotal de un producto
function calcularSubtotal(productoItem) {
    const cantidadInput = productoItem.querySelector('.cantidad-input');
    const precioRef = productoItem.querySelector('.precio-referencia');
    const subtotalInput = productoItem.querySelector('.subtotal-estimado');
    
    const cantidad = parseFloat(cantidadInput.value) || 0;
    const precioTexto = precioRef.value.replace('Q ', '').replace(',', '');
    const precio = parseFloat(precioTexto) || 0;
    
    const subtotal = cantidad * precio;
    subtotalInput.value = 'Q ' + subtotal.toFixed(2);
    
    calcularTotalGeneral();
}

// Calcular total general
function calcularTotalGeneral() {
    let total =
