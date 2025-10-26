@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-tags me-2"></i>Gestión de Categorías de Productos</h2>
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
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-tags fs-1 text-primary"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Total Categorías</h6>
            <h3 class="mb-0">{{ $categorias->total() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-check-circle fs-1 text-success"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Activas</h6>
            <h3 class="mb-0">{{ $categorias->where('activo', true)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-box-seam fs-1 text-info"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Con Productos</h6>
            <h3 class="mb-0">{{ $categorias->where('productos_count', '>', 0)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.categorias.index') }}" class="row g-3">
      <div class="col-md-8">
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por código, nombre o descripción..." value="{{ request('buscar') }}">
      </div>
      <div class="col-md-2">
        <select name="activo" class="form-select">
          <option value="">Todas</option>
          <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
          <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
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
    <span><i class="bi bi-table me-2"></i>Listado de Categorías</span>
    <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Nueva Categoría
    </a>
  </div>
  <div class="card-body p-0">
    @if($categorias->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th class="text-center">Productos</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categorias as $cat)
              <tr>
                <td><span class="badge bg-secondary">{{ $cat->codigo }}</span></td>
                <td><strong>{{ $cat->nombre }}</strong></td>
                <td><small>{{ $cat->descripcion ?? '-' }}</small></td>
                <td class="text-center"><span class="badge bg-info">{{ $cat->productos_count }}</span></td>
                <td class="text-center">
                  @if($cat->activo)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activa</span>
                  @else
                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactiva</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.categorias.edit', $cat->id_categoria) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.categorias.destroy', $cat->id_categoria) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta categoría?')">
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
        <p class="text-muted mt-3">No se encontraron categorías</p>
      </div>
    @endif
  </div>
  @if($categorias->hasPages())
    <div class="card-footer bg-white">{{ $categorias->links() }}</div>
  @endif
</div>
@endsection
