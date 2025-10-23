@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p>Rol no reconocido: {{ $role ?? 'N/A' }}. Contacte al administrador.</p>
</div>
@endsection
