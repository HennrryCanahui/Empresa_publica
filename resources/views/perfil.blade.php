@extends('layouts.app')

@section('header')
    <h2 class="h4">{{ __('Mi Perfil') }}</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Información del Usuario') }}
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Nombre') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Actualizar Perfil') }}</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                {{ __('Actualizar Contraseña') }}
            </div>
            <div class="card-body">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">{{ __('Contraseña Actual') }}</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Nueva Contraseña') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('Confirmar Nueva Contraseña') }}</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Actualizar Contraseña') }}</button>
                </form>
            </div>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                {{ __('Eliminar Cuenta') }}
            </div>
            <div class="card-body">
                <p class="card-text">{{ __('Una vez que tu cuenta sea eliminada, todos los recursos y datos serán eliminados permanentemente.') }}</p>
                <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">{{ __('Confirma tu contraseña') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="delete_password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-danger">{{ __('Eliminar Cuenta') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection