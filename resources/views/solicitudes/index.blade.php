@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Nueva Solicitud</h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('solicitudes.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="id_unida_solicitante" class="form-label">Unidad Solicitante <span class="text-danger">*</span></label>
                        <select name="id_unida_solicitante" 
                                id="id_unida_solicitante" 
                                class="form-select @error('id_unida_solicitante') is-invalid @enderror" 
                                required>
                            <option value="">Seleccione una unidad</option>
                            {{-- Aquí deberías cargar las unidades desde el controlador --}}
                            {{-- Ejemplo: @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id_unidad }}" {{ old('id_unida_solicitante') == $unidad->id_unidad ? 'selected' : '' }}>
                                    {{ $unidad->nombre }}
                                </option>
                            @endforeach --}}
                        </select>
                        @error('id_unida_solicitante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="4" 
                                  class="form-control @error('descripcion') is-invalid @enderror" 
                                  placeholder="Describe detalladamente lo que necesitas..."
                                  required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Describe claramente qué necesitas adquirir o el servicio que requieres.</small>
                    </div>

                    <div class="mb-3">
                        <label for="justificacion" class="form-label">Justificación <span class="text-danger">*</span></label>
                        <textarea name="justificacion" 
                                  id="justificacion" 
                                  rows="4" 
                                  class="form-control @error('justificacion') is-invalid @enderror" 
                                  placeholder="Explica por qué es necesaria esta solicitud..."
                                  required>{{ old('justificacion') }}</textarea>
                        @error('justificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Explica por qué es necesaria esta adquisición.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                            <select name="prioridad" 
                                    id="prioridad" 
                                    class="form-select @error('prioridad') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccione...</option>
                                <option value="Baja" {{ old('prioridad') == 'Baja' ? 'selected' : '' }}>Baja</option>
                                <option value="Media" {{ old('prioridad') == 'Media' ? 'selected' : '' }}>Media</option>
                                <option value="Alta" {{ old('prioridad') == 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Urgente" {{ old('prioridad') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('prioridad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="monto_total_estimado" class="form-label">Monto Estimado (Q)</label>
                            <input type="number" 
                                   name="monto_total_estimado" 
                                   id="monto_total_estimado" 
                                   class="form-control @error('monto_total_estimado') is-invalid @enderror" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('monto_total_estimado') }}"
                                   placeholder="0.00">
                            @error('monto_total_estimado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Monto aproximado de la adquisición.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_limitie" class="form-label">Fecha Límite</label>
                        <input type="date" 
                               name="fecha_limitie" 
                               id="fecha_limitie" 
                               class="form-control @error('fecha_limitie') is-invalid @enderror"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               value="{{ old('fecha_limitie') }}">
                        @error('fecha_limitie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Fecha en la que necesitas que esté completada la solicitud.</small>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('solicitudes.mias') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Crear Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection