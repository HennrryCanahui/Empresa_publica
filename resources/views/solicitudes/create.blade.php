@extends('layouts.app')

@section('header')
    <h2 class="h4">Crear Solicitud</h2>
@endsection

@section('content')
    <form method="POST" action="{{ route('solicitudes.store') }}">
        @csrf
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input id="titulo" name="titulo" class="form-control" value="{{ old('titulo') }}" required>
            @error('titulo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required>{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="monto_estimated" class="form-label">Monto estimado</label>
            <input id="monto_estimated" name="monto_estimated" class="form-control" value="{{ old('monto_estimated') }}" type="number" step="0.01">
            @error('monto_estimated')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid">
            <button class="btn btn-primary">Crear</button>
        </div>
    </form>
@endsection
