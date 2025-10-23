@extends('layouts.app')

@section('header')
    <h2 class="h4">Panel de Autoridad</h2>
@endsection

@section('content')
<div class="alert alert-secondary">Bienvenido, {{ Auth::user()->nombre }}. Aquí puedes aprobar o rechazar solicitudes.</div>
<ul class="list-group mb-4">
    <li class="list-group-item">Solicitudes para aprobar</li>
    <li class="list-group-item">Comparar cotizaciones</li>
    <li class="list-group-item">Historial de aprobaciones</li>
</ul>
@endsection
