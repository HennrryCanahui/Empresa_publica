@extends('layouts.app')

@section('header')
    <h2 class="h4">Panel de Solicitante</h2>
@endsection

@section('content')
      @php
            $user = Auth::user();
        @endphp
        @if($user && $user->rol === 'Solicitante')
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-plus-circle display-4 text-primary"></i>
                            <h5 class="card-title mt-2">Nueva Solicitud</h5>
                            <p class="card-text">Crea una nueva solicitud de pedido.</p>
                            <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">Crear</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-list-check display-4 text-success"></i>
                            <h5 class="card-title mt-2">Mis Solicitudes</h5>
                            <p class="card-text">Consulta el estado de tus solicitudes.</p>
                            <a href="{{ route('solicitudes.index') }}" class="btn btn-success">Ver</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-clock-history display-4 text-warning"></i>
                            <h5 class="card-title mt-2">Historial</h5>
                            <p class="card-text">Revisa el historial de tus solicitudes.</p>
                            <a href="{{ route('solicitudes.show') }}" class="btn btn-warning text-white">Historial</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="bi bi-x-circle display-4 text-danger"></i>
                            <h5 class="card-title mt-2">Cancelar Solicitud</h5>
                            <p class="card-text">Cancela solicitudes pendientes.</p>
                            <a href="{{ route('solicitudes.cancelar') }}" class="btn btn-danger">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        @endif
    </div>
@endsection