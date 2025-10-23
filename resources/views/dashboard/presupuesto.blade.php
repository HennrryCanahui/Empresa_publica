@extends('layouts.app')

@section('header')
    <h2 class="h4">Panel de Presupuesto</h2>
@endsection

@section('content')
<div class="alert alert-warning">Bienvenido, {{ Auth::user()->nombre }}. Aquí puedes validar solicitudes y gestionar presupuesto.</div>
<ul class="list-group mb-4">
    <li class="list-group-item">Solicitudes pendientes</li>
    <li class="list-group-item">Validar presupuesto</li>
    <li class="list-group-item">Reportes de presupuesto</li>
</ul>
@endsection
