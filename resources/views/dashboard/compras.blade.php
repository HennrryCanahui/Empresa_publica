@extends('layouts.app')

@section('header')
    <h2 class="h4">Panel de Compras</h2>
@endsection

@section('content')
<div class="alert alert-info">Bienvenido, {{ Auth::user()->nombre }}. Aquí puedes gestionar cotizaciones y órdenes de compra.</div>
<ul class="list-group mb-4">
    <li class="list-group-item">Solicitudes para cotizar</li>
    <li class="list-group-item">Gestión de cotizaciones</li>
    <li class="list-group-item">Órdenes de compra</li>
    <li class="list-group-item">Proveedores</li>
</ul>
@endsection
