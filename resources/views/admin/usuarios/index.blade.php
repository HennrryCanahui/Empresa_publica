@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h2>
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
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <i class="bi bi-people fs-1 text-primary"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Total Usuarios</h6>
            <h3 class="mb-0">{{ $usuarios->total() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <i class="bi bi-check-circle fs-1 text-success"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Activos</h6>
            <h3 class="mb-0">{{ $usuarios->where('activo', true)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <i class="bi bi-x-circle fs-1 text-danger"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Inactivos</h6>
            <h3 class="mb-0">{{ $usuarios->where('activo', false)->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <i class="bi bi-shield-check fs-1 text-info"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="text-muted mb-1">Administradores</h6>
            <h3 class="mb-0">{{ $usuarios->where('rol', 'Admin')->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filtros y búsqueda -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-3">
      <div class="col-md-3">
        <label class="form-label small">Buscar</label>
        <input type="text" name="buscar" class="form-control" placeholder="Nombre, apellido o correo..." value="{{ request('buscar') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label small">Rol</label>
        <select name="rol" class="form-select">
          <option value="">Todos</option>
          @foreach($roles as $rol)
            <option value="{{ $rol }}" {{ request('rol') == $rol ? 'selected' : '' }}>{{ $rol }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Unidad</label>
        <select name="id_unidad" class="form-select">
          <option value="">Todas</option>
          @foreach($unidades as $u)
            <option value="{{ $u->id_unidad }}" {{ request('id_unidad') == $u->id_unidad ? 'selected' : '' }}>{{ $u->nombre_unidad }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small">Estado</label>
        <select name="activo" class="form-select">
          <option value="">Todos</option>
          <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
          <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Buscar</button>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
      </div>
    </form>
  </div>
</div>

<!-- Tabla de usuarios -->
<div class="card">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span><i class="bi bi-table me-2"></i>Listado de Usuarios</span>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Nuevo Usuario
    </a>
  </div>
  <div class="card-body p-0">
    @if($usuarios->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nombre Completo</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Unidad</th>
              <th>Teléfono</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($usuarios as $usuario)
              <tr>
                <td><small class="text-muted">#{{ $usuario->id_usuario }}</small></td>
                <td>
                  <strong>{{ $usuario->nombre }} {{ $usuario->apellido }}</strong>
                </td>
                <td><small>{{ $usuario->correo }}</small></td>
                <td>
                  @if($usuario->rol == 'Admin')
                    <span class="badge bg-danger">{{ $usuario->rol }}</span>
                  @elseif($usuario->rol == 'Autoridad')
                    <span class="badge bg-success">{{ $usuario->rol }}</span>
                  @elseif($usuario->rol == 'Presupuesto')
                    <span class="badge bg-warning text-dark">{{ $usuario->rol }}</span>
                  @elseif($usuario->rol == 'Compras')
                    <span class="badge bg-info">{{ $usuario->rol }}</span>
                  @else
                    <span class="badge bg-secondary">{{ $usuario->rol }}</span>
                  @endif
                </td>
                <td><small>{{ $usuario->unidad->nombre_unidad ?? 'Sin unidad' }}</small></td>
                <td><small>{{ $usuario->telefono ?? '-' }}</small></td>
                <td class="text-center">
                  @if($usuario->activo)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                  @else
                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactivo</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}" class="btn btn-outline-primary" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de desactivar este usuario?')">
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
        <p class="text-muted mt-3">No se encontraron usuarios</p>
      </div>
    @endif
  </div>
  @if($usuarios->hasPages())
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          Mostrando {{ $usuarios->firstItem() }} - {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} registros
        </small>
        {{ $usuarios->links() }}
      </div>
    </div>
  @endif
</div>
@endsection
