@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input id="nombre" class="form-control" type="text" name="nombre" value="{{ old('nombre') }}" required autofocus>
            @if($errors->has('nombre'))
                <div class="invalid-feedback d-block">{{ $errors->first('nombre') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">{{ __('Apellido') }}</label>
            <input id="apellido" class="form-control" type="text" name="apellido" value="{{ old('apellido') }}" required>
            @if($errors->has('apellido'))
                <div class="invalid-feedback d-block">{{ $errors->first('apellido') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">{{ __('Correo') }}</label>
            <input id="correo" class="form-control" type="email" name="correo" value="{{ old('correo') }}" required>
            @if($errors->has('correo'))
                <div class="invalid-feedback d-block">{{ $errors->first('correo') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
            <input id="telefono" class="form-control" type="text" name="telefono" value="{{ old('telefono') }}" required>
            @if($errors->has('telefono'))
                <div class="invalid-feedback d-block">{{ $errors->first('telefono') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="id_unidad" class="form-label">{{ __('Unidad') }}</label>
            <select id="id_unidad" class="form-select" name="id_unidad" required>
                <option value="">Seleccione una unidad</option>
                @foreach(\App\Models\Unidad::all() as $unidad)
                    <option value="{{ $unidad->id_unidad }}" {{ old('id_unidad') == $unidad->id_unidad ? 'selected' : '' }}>
                        {{ $unidad->nombre }}
                    </option>
                @endforeach
            </select>
            @if($errors->has('id_unidad'))
                <div class="invalid-feedback d-block">{{ $errors->first('id_unidad') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">{{ __('Rol') }}</label>
            <select id="rol" class="form-select" name="rol" required>
                <option value="">Seleccione un rol</option>
                <option value="Solicitante" {{ old('rol') == 'Solicitante' ? 'selected' : '' }}>Solicitante</option>
                <option value="Compras" {{ old('rol') == 'Compras' ? 'selected' : '' }}>Compras</option>
                <option value="Presupuesto" {{ old('rol') == 'Presupuesto' ? 'selected' : '' }}>Presupuesto</option>
                <option value="Autoridad" {{ old('rol') == 'Autoridad' ? 'selected' : '' }}>Autoridad</option>
            </select>
            @if($errors->has('rol'))
                <div class="invalid-feedback d-block">{{ $errors->first('rol') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="contrasena" class="form-label">{{ __('Contraseña') }}</label>
            <input id="contrasena" class="form-control" type="password" name="contrasena" required>
            @if($errors->has('contrasena'))
                <div class="invalid-feedback d-block">{{ $errors->first('contrasena') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="contrasena_confirmation" class="form-label">{{ __('Confirmar Contraseña') }}</label>
            <input id="contrasena_confirmation" class="form-control" type="password" name="contrasena_confirmation" required>
            @if($errors->has('contrasena_confirmation'))
                <div class="invalid-feedback d-block">{{ $errors->first('contrasena_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="text-decoration-none" href="{{ route('login') }}">{{ __('Already registered?') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
        </div>
    </form>
@endsection
