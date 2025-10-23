@extends('layouts.app')

@section('header')
    <h2 class="h4">Panel de Administración</h2>
@endsection

@section('content')
<div class="alert alert-primary">Bienvenido, {{ Auth::user()->nombre }}. Este es el panel principal para administradores.</div>
<ul class="list-group mb-4">
    <li class="list-group-item">Gestión de usuarios</li>
    <li class="list-group-item">Gestión de unidades</li>
    <li class="list-group-item">Gestión de catálogo</li>
    <li class="list-group-item">Reportes y auditoría</li>
    <li class="list-group-item">Configuración del sistema</li>
</ul>
@endsection
