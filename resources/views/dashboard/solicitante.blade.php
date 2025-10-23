@extends('layouts.app')

@section('header')
    <h2 class="h4">Panel de Solicitante</h2>
@endsection

@section('content')
<div class="alert alert-success">Bienvenido, {{ Auth::user()->nombre }}. Aquí puedes crear y consultar tus solicitudes.</div>
<ul class="list-group mb-4">
    <li class="list-group-item">Crear nueva solicitud</li>
    <li class="list-group-item">Ver mis solicitudes</li>
    <li class="list-group-item">Notificaciones</li>
</ul>
@endsection