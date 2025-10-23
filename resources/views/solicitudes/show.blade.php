@extends('layouts.app')

@section('header')
    <h2 class="h4">Detalle de Solicitud</h2>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $solicitud->titulo }}</h5>
            <p class="card-text">{{ $solicitud->descripcion }}</p>
            <p><strong>Estado:</strong> {{ $solicitud->estado }}</p>
            <p><strong>Creada:</strong> {{ $solicitud->created_at->format('Y-m-d') }}</p>

            <a href="{{ route('solicitudes.mias') }}" class="btn btn-secondary">Volver</a>
            @can('update', $solicitud)
                <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-primary">Editar</a>
            @endcan
        </div>
    </div>
@endsection
