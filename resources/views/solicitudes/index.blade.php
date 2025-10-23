@extends('layouts.app')

@section('header')
    <h2 class="h4">Mis Solicitudes</h2>
@endsection

@section('content')
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">Crear Solicitud</a>
    </div>

    @if($solicitudes->isEmpty())
        <div class="alert alert-info">No tienes solicitudes aún.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Creada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $s)
                        <tr>
                            <td>{{ $s->id ?? $loop->iteration }}</td>
                            <td>{{ $s->titulo }}</td>
                            <td>{{ $s->estado }}</td>
                            <td>{{ $s->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('solicitudes.show', $s) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                @if($s->estado === 'Pendiente')
                                    <a href="{{ route('solicitudes.edit', $s) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                @endif
                                @if($s->estado === 'Rechazada')
                                    <form method="POST" action="{{ route('solicitudes.reabrir', $s) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Reabrir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
