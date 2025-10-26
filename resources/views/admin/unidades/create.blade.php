@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-building-add me-2"></i>Crear Nueva Unidad</h2>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
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
        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Información de la Unidad</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.unidades.store') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Unidad <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            @error('nombre')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
            <small class="form-text text-muted">Opcional: Breve descripción de las funciones de la unidad</small>
            @error('descripcion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}>
              <label class="form-check-label" for="activo">
                Unidad activa
              </label>
            </div>
          </div>

          <hr>

          <div class="d-flex justify-content-between">
            <a href="{{ route('admin.unidades.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Guardar Unidad
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

