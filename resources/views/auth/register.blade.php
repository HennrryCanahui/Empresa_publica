@extends('layouts.guest')

@section('content')
    <h2 class="h4 text-center mb-4">{{ __('Registro de Usuario') }}</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label text-muted">{{ __('Nombre') }}</label>
                <input id="nombre" class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}" 
                       type="text" name="nombre" value="{{ old('nombre') }}" required autofocus>
                @if($errors->has('nombre'))
                    <div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="apellido" class="form-label text-muted">{{ __('Apellido') }}</label>
                <input id="apellido" class="form-control{{ $errors->has('apellido') ? ' is-invalid' : '' }}" 
                       type="text" name="apellido" value="{{ old('apellido') }}" required>
                @if($errors->has('apellido'))
                    <div class="invalid-feedback">{{ $errors->first('apellido') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label text-muted">{{ __('Correo') }}</label>
                <input id="correo" class="form-control{{ $errors->has('correo') ? ' is-invalid' : '' }}" 
                       type="email" name="correo" value="{{ old('correo') }}" required>
                @if($errors->has('correo'))
                    <div class="invalid-feedback">{{ $errors->first('correo') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label text-muted">{{ __('Teléfono') }}</label>
                <input id="telefono" class="form-control{{ $errors->has('telefono') ? ' is-invalid' : '' }}" 
                       type="text" name="telefono" value="{{ old('telefono') }}" required>
                @if($errors->has('telefono'))
                    <div class="invalid-feedback">{{ $errors->first('telefono') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="id_unidad" class="form-label text-muted">{{ __('Unidad') }}</label>
                <select id="id_unidad" class="form-select{{ $errors->has('id_unidad') ? ' is-invalid' : '' }}" 
                        name="id_unidad" required>
                    <option value="">Seleccione una unidad</option>
                    @foreach(\App\Models\Unidad::all() as $unidad)
                        <option value="{{ $unidad->id_unidad }}" {{ old('id_unidad') == $unidad->id_unidad ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('id_unidad'))
                    <div class="invalid-feedback">{{ $errors->first('id_unidad') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="rol" class="form-label text-muted">{{ __('Rol') }}</label>
                <select id="rol" class="form-select{{ $errors->has('rol') ? ' is-invalid' : '' }}" 
                        name="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="Solicitante" {{ old('rol') == 'Solicitante' ? 'selected' : '' }}>Solicitante</option>
                    <option value="Compras" {{ old('rol') == 'Compras' ? 'selected' : '' }}>Compras</option>
                    <option value="Presupuesto" {{ old('rol') == 'Presupuesto' ? 'selected' : '' }}>Presupuesto</option>
                    <option value="Autoridad" {{ old('rol') == 'Autoridad' ? 'selected' : '' }}>Autoridad</option>
                </select>
                @if($errors->has('rol'))
                    <div class="invalid-feedback">{{ $errors->first('rol') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="contrasena" class="form-label text-muted">{{ __('Contraseña') }}</label>
                <input id="contrasena" class="form-control{{ $errors->has('contrasena') ? ' is-invalid' : '' }}" 
                       type="password" name="contrasena" required>
                @if($errors->has('contrasena'))
                    <div class="invalid-feedback">{{ $errors->first('contrasena') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="contrasena_confirmation" class="form-label text-muted">{{ __('Confirmar Contraseña') }}</label>
                <input id="contrasena_confirmation" class="form-control{{ $errors->has('contrasena_confirmation') ? ' is-invalid' : '' }}" 
                       type="password" name="contrasena_confirmation" required>
                @if($errors->has('contrasena_confirmation'))
                    <div class="invalid-feedback">{{ $errors->first('contrasena_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">{{ __('Registrar') }}</button>
        </div>

        <div class="text-center mt-4">
            <a class="text-decoration-none" href="{{ route('login') }}">
                {{ __('¿Ya tienes una cuenta?') }}
            </a>
        </div>
    </form>

        <div class="d-flex justify-content-between align-items-center">
            <a class="text-decoration-none" href="{{ route('login') }}">{{ __('Already registered?') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
        </div>
    </form>
@endsection
