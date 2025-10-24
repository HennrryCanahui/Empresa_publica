@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Categoría</h2>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
    @endif

    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.categorias.update', $categoria->id_categoria) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', $categoria->codigo) }}" required>
            @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ old('activo', $categoria->activo) ? 'checked' : '' }}>
              <label class="form-check-label" for="activo">Activa</label>
            </div>
          </div>
          <hr>
          <div class="d-flex justify-content-between">
            <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
