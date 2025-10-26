@extends('layouts.app')

@section('header')
    <h2 class="h4 mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Nueva Solicitud de Pedido</h2>
@endsection

@section('content')
<form action="{{ route('solicitudes.store') }}" method="POST" id="form-solicitud">
    @csrf
    
    <!-- Información General -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h5>
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
                                {{ old('id_unidad_solicitante', Auth::user()->id_unidad) == $unidad->id_unidad ? 'selected' : '' }}>
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
                        <option value="Baja" {{ old('prioridad') == 'Baja' ? 'selected' : '' }}>Baja</option>
                        <option value="Media" {{ old('prioridad', 'Media') == 'Media' ? 'selected' : '' }}>Media</option>
                        <option value="Alta" {{ old('prioridad') == 'Alta' ? 'selected' : '' }}>Alta</option>
                        <option value="Urgente" {{ old('prioridad') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
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
                           value="{{ old('fecha_limite') }}">
                    @error('fecha_limite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                <textarea name="descripcion" id="descripcion" rows="3"
                          class="form-control @error('descripcion') is-invalid @enderror"
                          placeholder="Describe detalladamente lo que necesitas..." required>{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="justificacion" class="form-label">Justificación <span class="text-danger">*</span></label>
                <textarea name="justificacion" id="justificacion" rows="3"
                          class="form-control @error('justificacion') is-invalid @enderror"
                          placeholder="Explica por qué es necesaria esta solicitud..." required>{{ old('justificacion') }}</textarea>
                @error('justificacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Productos/Servicios -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Productos/Servicios Solicitados</h5>
            <button type="button" class="btn btn-light btn-sm" id="btn-agregar-producto">
                <i class="bi bi-plus-circle me-1"></i>Agregar Producto
            </button>
        </div>
        <div class="card-body">
            <div id="productos-container">
                <!-- El primer producto se agrega por defecto -->
            </div>

            <div class="text-end mt-3">
                <h5>Total Estimado: <span id="total-general" class="text-primary">Q 0.00</span></h5>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>Crear Solicitud
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

let productoIndex = 0;

// Función para crear un nuevo item de producto
function crearProductoHTML(index) {
    let optionsHTML = '<option value="">Seleccione un producto...</option>';
    
    productosData.forEach(function(producto) {
        optionsHTML += '<option value="' + producto.id + '" ' +
                       'data-precio="' + producto.precio + '" ' +
                       'data-unidad="' + producto.unidad + '">' +
                       producto.codigo + ' - ' + producto.nombre +
                       '</option>';
    });

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
                           required>
                </div>
                <div class="col-md-1 mb-2">
                    <label class="form-label">Unidad</label>
                    <input type="text" class="form-control unidad-medida" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Precio Ref.</label>
                    <input type="text" class="form-control precio-referencia" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Subtotal Est.</label>
                    <input type="text" class="form-control subtotal-estimado" readonly>
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
                              placeholder="Detalles adicionales, características específicas, etc."></textarea>
                </div>
            </div>
        </div>
    `;
}

// Agregar el primer producto al cargar
document.addEventListener('DOMContentLoaded', function() {
    agregarProducto();
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
    let total = 0;
    document.querySelectorAll('.subtotal-estimado').forEach(function(input) {
        const valor = parseFloat(input.value.replace('Q ', '').replace(',', '')) || 0;
        total += valor;
    });
    
    document.getElementById('total-general').textContent = 'Q ' + total.toFixed(2);
}

// Eliminar producto (delegación de eventos)
document.getElementById('productos-container').addEventListener('click', function(e) {
    if (e.target.closest('.btn-eliminar-producto')) {
        const productoItem = e.target.closest('.producto-item');
        productoItem.remove();
        actualizarBotonesEliminar();
        calcularTotalGeneral();
    }
});

// Actualizar visibilidad de botones eliminar
function actualizarBotonesEliminar() {
    const items = document.querySelectorAll('.producto-item');
    items.forEach(function(item) {
        const btnEliminar = item.querySelector('.btn-eliminar-producto');
        if (items.length > 1) {
            btnEliminar.style.display = 'block';
        } else {
            btnEliminar.style.display = 'none';
        }
    });
}

// Validación antes de enviar
document.getElementById('form-solicitud').addEventListener('submit', function(e) {
    const productos = document.querySelectorAll('.producto-item');
    if (productos.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto');
        return false;
    }
    
    let valido = true;
    productos.forEach(function(item) {
        const select = item.querySelector('.producto-select');
        const cantidad = item.querySelector('.cantidad-input').value;
        
        if (!select.value) {
            valido = false;
            select.classList.add('is-invalid');
        }
        
        if (!cantidad || parseFloat(cantidad) <= 0) {
            valido = false;
            item.querySelector('.cantidad-input').classList.add('is-invalid');
        }
    });
    
    if (!valido) {
        e.preventDefault();
        alert('Por favor complete todos los campos requeridos de los productos');
        return false;
    }
});
</script>
@endsection
