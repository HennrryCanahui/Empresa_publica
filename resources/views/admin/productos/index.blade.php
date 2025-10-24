@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-box-seam me-2"></i>Catálogo de Productos</h2>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Estadísticas -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-box-seam fs-1 text-primary"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Total Productos</h6>
            <h3 class="mb-0">{{ $productos->total() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-check-circle fs-1 text-success"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Activos</h6>
            <h3 class="mb-0">{{ $productos->where('activo', true)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-cart fs-1 text-info"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Bienes</h6>
            <h3 class="mb-0">{{ $productos->where('tipo', 'Bien')->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-gear fs-1 text-warning"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Servicios</h6>
            <h3 class="mb-0">{{ $productos->where('tipo', 'Servicio')->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.productos.index') }}" class="row g-3">
      <div class="col-md-4">
        <input type="text" name="buscar" class="form-control" placeholder="Buscar código, nombre..." value="{{ request('buscar') }}">
      </div>
      <div class="col-md-2">
        <select name="id_categoria" class="form-select">
          <option value="">Todas las categorías</option>
          @foreach($categorias as $cat)
            <option value="{{ $cat->id_categoria }}" {{ request('id_categoria') == $cat->id_categoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="tipo" class="form-select">
          <option value="">Todos los tipos</option>
          @foreach($tipos as $tipo)
            <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="activo" class="form-select">
          <option value="">Todos</option>
          <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
          <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
      </div>
    </form>
  </div>
</div>

<!-- Tabla -->
<div class="card">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span><i class="bi bi-table me-2"></i>Listado de Productos</span>
    <a href="{{ route('admin.productos.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Nuevo Producto
    </a>
  </div>
  <div class="card-body p-0">
    @if($productos->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Tipo</th>
              <th>Unidad</th>
              <th class="text-end">Precio Ref.</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($productos as $prod)
              <tr>
                <td><span class="badge bg-secondary">{{ $prod->codigo }}</span></td>
                <td><strong>{{ $prod->nombre }}</strong><br><small class="text-muted">{{ Str::limit($prod->descripcion, 40) }}</small></td>
                <td><small>{{ $prod->categoria->nombre ?? '-' }}</small></td>
                <td>
                  @if($prod->tipo == 'Bien')
                    <span class="badge bg-info">{{ $prod->tipo }}</span>
                  @elseif($prod->tipo == 'Servicio')
                    <span class="badge bg-warning text-dark">{{ $prod->tipo }}</span>
                  @else
                    <span class="badge bg-secondary">{{ $prod->tipo }}</span>
                  @endif
                </td>
                <td><small>{{ $prod->unidad_medida }}</small></td>
                <td class="text-end"><small>{{ $prod->precio_referencia ? 'Q ' . number_format($prod->precio_referencia, 2) : '-' }}</small></td>
                <td class="text-center">
                  @if($prod->activo)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
                  @else
                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i></span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.productos.edit', $prod->id_producto) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.productos.destroy', $prod->id_producto) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="text-muted mt-3">No se encontraron productos</p>
      </div>
    @endif
  </div>
  @if($productos->hasPages())
    <div class="card-footer bg-white">{{ $productos->links() }}</div>
  @endif
</div>
@endsection
