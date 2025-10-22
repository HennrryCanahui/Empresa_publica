@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-box"></i> Catálogo de Productos</h2>
    </div>
    <div class="col-md-4 text-end">
        @can('crear-productos')
        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="buscar" 
                           value="{{ request('buscar') }}" placeholder="Buscar por nombre o código">
                </div>
            </div>
            
            <div class="col-md-3">
                <select class="form-select" name="categoria">
                    <option value="">-- Todas las categorías --</option>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id_categoria }}" 
                            {{ request('categoria') == $categoria->id_categoria ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <select class="form-select" name="tipo">
                    <option value="">-- Todos los tipos --</option>
                    @foreach($tipos as $key => $tipo)
                    <option value="{{ $key }}" {{ request('tipo') == $key ? 'selected' : '' }}>
                        {{ $tipo }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Unidad</th>
                        <th>Precio Ref.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>
                            {{ $producto->nombre }}
                            @if($producto->descripcion)
                            <i class="bi bi-info-circle text-info" 
                               data-bs-toggle="tooltip" 
                               title="{{ $producto->descripcion }}"></i>
                            @endif
                        </td>
                        <td>{{ $producto->categoria->nombre }}</td>
                        <td>{{ $tipos[$producto->tipo] }}</td>
                        <td>{{ $producto->unidad_medida }}</td>
                        <td>
                            @if($producto->precio_referencia)
                                {{ number_format($producto->precio_referencia, 2) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('editar-productos')
                                <a href="{{ route('productos.edit', $producto) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                
                                @can('eliminar-productos')
                                <form action="{{ route('productos.destroy', $producto) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="tooltip"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron productos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $productos->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush
@endsection
