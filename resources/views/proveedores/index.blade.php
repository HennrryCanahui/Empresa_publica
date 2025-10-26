@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-building me-2"></i>Gestión de Proveedores</h2>
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
          <div class="flex-shrink-0"><i class="bi bi-building fs-1 text-primary"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Total Proveedores</h6>
            <h3 class="mb-0">{{ $proveedores->total() }}</h3>
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
            <h6 class="text-muted mb-1">Activos</h6>
            <h3 class="mb-0">{{ $proveedores->where('activo', true)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0"><i class="bi bi-x-circle fs-1 text-danger"></i></div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Inactivos</h6>
            <h3 class="mb-0">{{ $proveedores->where('activo', false)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('proveedores.index') }}" class="row g-3">
      <div class="col-md-8">
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por razón social, NIT o código..." value="{{ request('buscar') }}">
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
    <span><i class="bi bi-table me-2"></i>Listado de Proveedores</span>
    <a href="{{ route('proveedores.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Nuevo Proveedor
    </a>
  </div>
  <div class="card-body p-0">
    @if($proveedores->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Código</th>
              <th>Razón Social</th>
              <th>NIT</th>
              <th>Contacto Principal</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($proveedores as $proveedor)
              <tr>
                <td><span class="badge bg-secondary">{{ $proveedor->codigo_proveedor }}</span></td>
                <td><strong>{{ $proveedor->razon_social }}</strong></td>
                <td>{{ $proveedor->nit }}</td>
                <td><small>{{ $proveedor->contacto_principal ?? '-' }}</small></td>
                <td><small>{{ $proveedor->telefono ?? '-' }}</small></td>
                <td><small>{{ $proveedor->correo ?? '-' }}</small></td>
                <td class="text-center">
                  @if($proveedor->activo)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                  @else
                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactivo</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('proveedores.show', $proveedor->id_proveedor) }}" class="btn btn-outline-info" title="Ver">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-outline-primary" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de desactivar este proveedor?')">
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
        <p class="text-muted mt-3">No se encontraron proveedores</p>
      </div>
    @endif
  </div>
  @if($proveedores->hasPages())
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          Mostrando {{ $proveedores->firstItem() }} - {{ $proveedores->lastItem() }} de {{ $proveedores->total() }} registros
        </small>
        {{ $proveedores->links() }}
      </div>
    </div>
  @endif
</div>
@endsection
