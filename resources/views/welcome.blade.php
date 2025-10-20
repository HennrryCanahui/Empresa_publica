@extends("layouts.app")

@section("content")
    <h1>Welcome to the Application</h1>
    <p>This is the welcome page.</p>
    <a href="{{ route('test.index') }}" class="btn btn-primary">Ir a la tabla de pruebas</a>
@endsection