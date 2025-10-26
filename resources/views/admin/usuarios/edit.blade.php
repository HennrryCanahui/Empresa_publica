@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h2>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <!-- Alertas de error -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
      <h6><i class="bi bi-exclamation-circle me-2"></i>Por favor corrija los siguientes errores:</h6>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="card">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Información del Usuario</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
              @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" required>
              @error('apellido')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('correo') is-invalid @enderror" id="correo" name="correo" value="{{ old('correo', $usuario->correo) }}" required>
              @error('correo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}">
              @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <small>Deje los campos de contraseña vacíos si no desea cambiarla</small>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="contrasena" class="form-label">Nueva Contraseña</label>
              <input type="password" class="form-control @error('contrasena') is-invalid @enderror" id="contrasena" name="contrasena">
              <small class="form-text text-muted">Mínimo 8 caracteres</small>
              @error('contrasena')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="contrasena_confirmation" class="form-label">Confirmar Contraseña</label>
              <input type="password" class="form-control" id="contrasena_confirmation" name="contrasena_confirmation">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
              <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                <option value="">-- Seleccione un rol --</option>
                @foreach($roles as $rol)
                  <option value="{{ $rol }}" {{ old('rol', $usuario->rol) == $rol ? 'selected' : '' }}>{{ $rol }}</option>
                @endforeach
              </select>
              @error('rol')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="id_unidad" class="form-label">Unidad <span class="text-danger">*</span></label>
              <select class="form-select @error('id_unidad') is-invalid @enderror" id="id_unidad" name="id_unidad" required>
                <option value="">-- Seleccione una unidad --</option>
                @foreach($unidades as $unidad)
                  <option value="{{ $unidad->id_unidad }}" {{ old('id_unidad', $usuario->id_unidad) == $unidad->id_unidad ? 'selected' : '' }}>
                    {{ $unidad->nombre }}
                  </option>
                @endforeach
              </select>
              @error('id_unidad')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
              <label class="form-check-label" for="activo">
                Usuario activo
              </label>
            </div>
          </div>

          <hr>

          <div class="d-flex justify-content-between">
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Actualizar Usuario
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
