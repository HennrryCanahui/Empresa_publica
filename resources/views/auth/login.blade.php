@extends('layouts.guest')

@section('content')
    <h2 class="h4 text-center mb-4">{{ __('Iniciar Sesión') }}</h2>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="correo" class="form-label text-muted">{{ __('Correo') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.708 2.825L15 11.105V5.383zm-.034 6.876l-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741zM1 11.105l4.708-2.897L1 5.383v5.722z"/>
                    </svg>
                </span>
                <input id="correo" class="form-control{{ $errors->has('correo') ? ' is-invalid' : '' }}" 
                       type="email" name="correo" value="{{ old('correo') }}" 
                       required autofocus autocomplete="username">
            </div>
            @if($errors->has('correo'))
                <div class="invalid-feedback d-block">{{ $errors->first('correo') }}</div>
            @endif
        </div>

        <div class="mb-4">
            <label for="contrasena" class="form-label text-muted">{{ __('Contraseña') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                    </svg>
                </span>
                <input id="contrasena" class="form-control{{ $errors->has('contrasena') ? ' is-invalid' : '' }}" 
                       type="password" name="contrasena" 
                       required autocomplete="current-password">
            </div>
            @if($errors->has('contrasena'))
                <div class="invalid-feedback d-block">{{ $errors->first('contrasena') }}</div>
            @endif
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label text-muted">
                    {{ __('Recordarme') }}
                </label>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                {{ __('Iniciar Sesión') }}
            </button>
        </div>
{{--        @if (Route::has('password.request'))
        @if (Route::has('password.request'))
            <div class="text-center mt-4">
                <a class="text-decoration-none" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            </div>
        @endif
        ----- IGNORE ---}}
    </form>
@endsection
