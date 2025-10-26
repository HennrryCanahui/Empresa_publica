@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-pencil me-2"></i>Editar Proveedor</h2>
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Listado
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Errores de validación:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-pencil me-2"></i>Editar Proveedor: {{ $proveedor->razon_social }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('proveedores.update', $proveedor->id_proveedor) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Código del Proveedor -->
                <div class="col-md-6 mb-3">
                    <label for="codigo" class="form-label">
                        Código del Proveedor <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('codigo') is-invalid @enderror" 
                        id="codigo" 
                        name="codigo" 
                        value="{{ old('codigo', $proveedor->codigo) }}" 
                        required
                        placeholder="Ej: PROV-001"
                        maxlength="20">
                    @error('codigo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Código único identificador del proveedor</small>
                </div>

                <!-- NIT/RFC -->
                <div class="col-md-6 mb-3">
                    <label for="nit_rfc" class="form-label">
                        NIT <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('nit_rfc') is-invalid @enderror" 
                        id="nit_rfc" 
                        name="nit_rfc" 
                        value="{{ old('nit_rfc', $proveedor->nit_rfc) }}" 
                        required
                        placeholder="Ej: 1234567-8"
                        maxlength="20">
                    @error('nit_rfc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Número de Identificación Tributaria</small>
                </div>

                <!-- Razón Social -->
                <div class="col-md-12 mb-3">
                    <label for="razon_social" class="form-label">
                        Razón Social <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('razon_social') is-invalid @enderror" 
                        id="razon_social" 
                        name="razon_social" 
                        value="{{ old('razon_social', $proveedor->razon_social) }}" 
                        required
                        placeholder="Nombre legal de la empresa"
                        maxlength="200">
                    @error('razon_social')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="col-md-12 mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <textarea 
                        class="form-control @error('direccion') is-invalid @enderror" 
                        id="direccion" 
                        name="direccion" 
                        rows="2"
                        placeholder="Dirección completa del proveedor"
                        maxlength="500">{{ old('direccion', $proveedor->direccion) }}</textarea>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input 
                        type="text" 
                        class="form-control @error('telefono') is-invalid @enderror" 
                        id="telefono" 
                        name="telefono" 
                        value="{{ old('telefono', $proveedor->telefono) }}"
                        placeholder="Ej: 2345-6789"
                        maxlength="20">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Correo -->
                <div class="col-md-6 mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input 
                        type="email" 
                        class="form-control @error('correo') is-invalid @enderror" 
                        id="correo" 
                        name="correo" 
                        value="{{ old('correo', $proveedor->correo) }}"
                        placeholder="correo@proveedor.com"
                        maxlength="100">
                    @error('correo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contacto Principal -->
                <div class="col-md-6 mb-3">
                    <label for="contacto_principal" class="form-label">Contacto Principal</label>
                    <input 
                        type="text" 
                        class="form-control @error('contacto_principal') is-invalid @enderror" 
                        id="contacto_principal" 
                        name="contacto_principal" 
                        value="{{ old('contacto_principal', $proveedor->contacto_principal) }}"
                        placeholder="Nombre del contacto principal"
                        maxlength="150">
                    @error('contacto_principal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado Activo -->
                <div class="col-md-6 mb-3">
                    <label for="activo" class="form-label">Estado</label>
                    <div class="form-check form-switch mt-2">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="activo" 
                            name="activo" 
                            value="1"
                            {{ old('activo', $proveedor->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">
                            <i class="bi bi-check-circle text-success me-1"></i>Proveedor Activo
                        </label>
                    </div>
                    <small class="text-muted">Los proveedores inactivos no podrán cotizar</small>
                </div>
            </div>

            <hr>

            <!-- Botones -->
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Actualizar Proveedor
                    </button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Convertir código a mayúsculas automáticamente
    document.getElementById('codigo').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validación de NIT (solo números y guión)
    document.getElementById('nit_rfc').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9-]/g, '');
    });

    // Validación de teléfono (solo números y guión)
    document.getElementById('telefono').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9-]/g, '');
    });
</script>
@endpush
@endsection
