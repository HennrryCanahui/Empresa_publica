@extends("layouts.app")

@section("title","Perfil de Usuario")
@section("content")
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title
    mb-0">Mi Perfil</h4>
                
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
                <p><strong>Correo Electrónico:</strong> {{ Auth::user()->correo }}</p>
                <p><strong>Rol:</strong> {{ Auth::user()->rol }}</p>
            </div>
        </div>
    </div>
</div>
@endsection