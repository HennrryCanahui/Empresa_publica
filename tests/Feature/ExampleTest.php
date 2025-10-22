<?php

use App\Models\Usuario;
use App\Models\Unidad;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('redirects to login when not authenticated', function () {
    $response = $this->get('/');
    $response->assertStatus(302)
             ->assertRedirect('/login');
});

test('can access dashboard when authenticated', function () {
    $unidad = Unidad::factory()->create();
    $user = Usuario::factory()->create([
        'rol' => 'Administrador',
        'id_unidad' => $unidad->id_unidad
    ]);

    $response = $this->actingAs($user)
                     ->get('/');

    $response->assertStatus(200);
});
