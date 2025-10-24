@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h2>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    @if($errors->any())
    <div class="alert alert-danger">
      <h6>Errores:</h6>
      <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
    @endif

    <div class="card">
      <div class="card-header bg-white"><h5 class="mb-0">Información del Producto</h5></div>
      <div class="card-body">
        <form action="{{ route('admin.productos.update', $producto->id_producto) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" required>
              @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8 mb-3">
              <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
              <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                <option value="">-- Seleccione --</option>
                @foreach($tipos as $tipo)
                  <option value="{{ $tipo }}" {{ old('tipo', $producto->tipo) == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                @endforeach
              </select>
              @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
              <select class="form-select @error('id_categoria') is-invalid @enderror" id="id_categoria" name="id_categoria" required>
                <option value="">-- Seleccione --</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id_categoria }}" {{ old('id_categoria', $producto->id_categoria) == $cat->id_categoria ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                @endforeach
              </select>
              @error('id_categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label for="unidad_medida" class="form-label">Unidad Medida <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('unidad_medida') is-invalid @enderror" id="unidad_medida" name="unidad_medida" value="{{ old('unidad_medida', $producto->unidad_medida) }}" required>
              @error('unidad_medida')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="precio_referencia" class="form-label">Precio Referencia</label>
              <div class="input-group">
                <span class="input-group-text">Q</span>
                <input type="number" step="0.01" min="0" class="form-control" id="precio_referencia" name="precio_referencia" value="{{ old('precio_referencia', $producto->precio_referencia) }}">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label d-block">&nbsp;</label>
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Producto activo</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="especificaciones_tecnicas" class="form-label">Especificaciones Técnicas</label>
            <textarea class="form-control" id="especificaciones_tecnicas" name="especificaciones_tecnicas" rows="3">{{ old('especificaciones_tecnicas', $producto->especificaciones_tecnicas) }}</textarea>
          </div>

          <hr>
          <div class="d-flex justify-content-between">
            <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Actualizar Producto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
