@extends('layouts.guest')

@section('content')
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="correo" class="form-label">{{ __('Correo') }}</label>
            <input id="correo" class="form-control" type="email" name="correo" value="{{ old('correo') }}" required autofocus autocomplete="username">
            @if($errors->has('correo'))
                <div class="invalid-feedback d-block">{{ $errors->first('correo') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="contrasena" class="form-label">{{ __('Contraseña') }}</label>
            <input id="contrasena" class="form-control" type="password" name="contrasena" required autocomplete="current-password">
            @if($errors->has('contrasena'))
                <div class="invalid-feedback d-block">{{ $errors->first('contrasena') }}</div>
            @endif
        </div>

        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label">{{ __('Remember me') }}</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a class="text-decoration-none" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif

            <button type="submit" class="btn btn-primary">{{ __('Log in') }}</button>
        </div>
    </form>
@endsection
