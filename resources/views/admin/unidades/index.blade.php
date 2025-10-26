@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-building me-2"></i>Gestión de Unidades</h2>
@endsection

@section('content')
<!-- Alertas -->
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
          <div class="flex-shrink-0">
            <i class="bi bi-building fs-1 text-primary"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Total Unidades</h6>
            <h3 class="mb-0">{{ $unidades->total() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <i class="bi bi-check-circle fs-1 text-success"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Activas</h6>
            <h3 class="mb-0">{{ $unidades->where('activo', true)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <i class="bi bi-people fs-1 text-info"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Con Usuarios</h6>
            <h3 class="mb-0">{{ $unidades->where('usuarios_count', '>', 0)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filtros y búsqueda -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.unidades.index') }}" class="row g-3">
      <div class="col-md-6">
        <label class="form-label small">Buscar</label>
        <input type="text" name="buscar" class="form-control" placeholder="Nombre o descripción..." value="{{ request('buscar') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label small">Estado</label>
        <select name="activo" class="form-select">
          <option value="">Todas</option>
          <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
          <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Buscar</button>
        <a href="{{ route('admin.unidades.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
      </div>
    </form>
  </div>
</div>

<!-- Tabla -->
<div class="card">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span><i class="bi bi-table me-2"></i>Listado de Unidades</span>
    <a href="{{ route('admin.unidades.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Nueva Unidad
    </a>
  </div>
  <div class="card-body p-0">
    @if($unidades->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th class="text-center">Usuarios</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($unidades as $unidad)
              <tr>
                <td><small class="text-muted">#{{ $unidad->id_unidad }}</small></td>
                <td><strong>{{ $unidad->nombre }}</strong></td>
                <td><small>{{ $unidad->descripcion ?? 'Sin descripción' }}</small></td>
                <td class="text-center">
                  <span class="badge bg-info">{{ $unidad->usuarios_count }}</span>
                </td>
                <td class="text-center">
                  @if($unidad->activo)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activa</span>
                  @else
                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactiva</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.unidades.edit', $unidad->id_unidad) }}" class="btn btn-outline-primary" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.unidades.destroy', $unidad->id_unidad) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de desactivar esta unidad?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Desactivar">
                        <i class="bi bi-trash"></i>
                      </button>
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
        <p class="text-muted mt-3">No se encontraron unidades</p>
      </div>
    @endif
  </div>
  @if($unidades->hasPages())
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          Mostrando {{ $unidades->firstItem() }} - {{ $unidades->lastItem() }} de {{ $unidades->total() }} registros
        </small>
        {{ $unidades->links() }}
      </div>
    </div>
  @endif
</div>
@endsection

