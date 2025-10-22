@extends('layouts.app')

@section('title', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-box"></i> 
            {{ isset($producto) ? 'Editar Producto' : 'Nuevo Producto' }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($producto) ? route('productos.update', $producto) : route('productos.store') }}" 
                      method="POST">
                    @csrf
                    @if(isset($producto))
                        @method('PUT')
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código *</label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" name="codigo" 
                                   value="{{ old('codigo', $producto->codigo ?? '') }}" required>
                            @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" name="nombre" 
                                   value="{{ old('nombre', $producto->nombre ?? '') }}" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo *</label>
                            <select class="form-select @error('tipo') is-invalid @enderror" 
                                    id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                @foreach($tipos as $key => $tipo)
                                <option value="{{ $key }}" 
                                        {{ old('tipo', $producto->tipo ?? '') == $key ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                                @endforeach
                            </select>
                            @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="id_categoria" class="form-label">Categoría *</label>
                            <select class="form-select @error('id_categoria') is-invalid @enderror" 
                                    id="id_categoria" name="id_categoria" required>
                                <option value="">Seleccione...</option>
                                @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}"
                                        {{ old('id_categoria', $producto->id_categoria ?? '') == $categoria->id_categoria ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="unidad_medida" class="form-label">Unidad de Medida *</label>
                            <input type="text" class="form-control @error('unidad_medida') is-invalid @enderror" 
                                   id="unidad_medida" name="unidad_medida" 
                                   value="{{ old('unidad_medida', $producto->unidad_medida ?? '') }}" required>
                            @error('unidad_medida')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="precio_referencia" class="form-label">Precio de Referencia</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('precio_referencia') is-invalid @enderror" 
                                       id="precio_referencia" name="precio_referencia" 
                                       value="{{ old('precio_referencia', $producto->precio_referencia ?? '') }}">
                                @error('precio_referencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label for="especificaciones_tecnicas" class="form-label">Especificaciones Técnicas</label>
                            <textarea class="form-control @error('especificaciones_tecnicas') is-invalid @enderror" 
                                      id="especificaciones_tecnicas" name="especificaciones_tecnicas" 
                                      rows="3">{{ old('especificaciones_tecnicas', $producto->especificaciones_tecnicas ?? '') }}</textarea>
                            @error('especificaciones_tecnicas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> 
                                {{ isset($producto) ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection