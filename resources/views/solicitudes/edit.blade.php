@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Editar Solicitud</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Solicitud: <strong>{{ $solicitud->numero_solicitud }}</strong>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('solicitudes.update', $solicitud) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="4" 
                                  class="form-control @error('descripcion') is-invalid @enderror" 
                                  required>{{ old('descripcion', $solicitud->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="justificacion" class="form-label">Justificación <span class="text-danger">*</span></label>
                        <textarea name="justificacion" 
                                  id="justificacion" 
                                  rows="4" 
                                  class="form-control @error('justificacion') is-invalid @enderror" 
                                  required>{{ old('justificacion', $solicitud->justificacion) }}</textarea>
                        @error('justificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                            <select name="prioridad" 
                                    id="prioridad" 
                                    class="form-select @error('prioridad') is-invalid @enderror" 
                                    required>
                                <option value="Baja" {{ old('prioridad', $solicitud->prioridad) == 'Baja' ? 'selected' : '' }}>Baja</option>
                                <option value="Media" {{ old('prioridad', $solicitud->prioridad) == 'Media' ? 'selected' : '' }}>Media</option>
                                <option value="Alta" {{ old('prioridad', $solicitud->prioridad) == 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Urgente" {{ old('prioridad', $solicitud->prioridad) == 'Urgente' ? 'selected' : '' }}>Urgente</option>
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
                                   value="{{ old('monto_total_estimado', $solicitud->monto_total_estimado) }}"
                                   placeholder="0.00">
                            @error('monto_total_estimado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_limitie" class="form-label">Fecha Límite</label>
                        <input type="date" 
                               name="fecha_limitie" 
                               id="fecha_limitie" 
                               class="form-control @error('fecha_limitie') is-invalid @enderror"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               value="{{ old('fecha_limitie', $solicitud->fecha_limitie ? \Carbon\Carbon::parse($solicitud->fecha_limitie)->format('Y-m-d') : '') }}">
                        @error('fecha_limitie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection