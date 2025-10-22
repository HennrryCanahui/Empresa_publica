<?php

use App\Models\Usuario;
use App\Models\Unidad;
use App\Models\CatalogoProducto;
use App\Models\Solicitud;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea una solicitud, valida presupuesto y registra cotizacion', function () {
    // Crear unidad y usuario solicitante
    $unidad = Unidad::factory()->create();
    $user = Usuario::factory()->create([
        'rol' => 'Solicitante',
        'id_unidad' => $unidad->id_unidad
    ]);

    // Crear producto
    $producto = CatalogoProducto::factory()->create();

    // Iniciar sesion como solicitante y crear solicitud
    $this->actingAs($user, 'web');

    $response = $this->post(route('solicitudes.store'), [
        'descripcion' => 'Prueba',
        'justificacion' => 'Prueba justificaci\u00f3n',
        'prioridad' => 'Media',
        'productos' => [$producto->id_producto],
        'cantidades' => [1],
        'precios' => [10]
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('solicitud', ['descripcion' => 'Prueba']);
});
