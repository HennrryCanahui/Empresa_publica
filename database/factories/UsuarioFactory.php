<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = \App\Models\Usuario::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'correo' => $this->faker->unique()->safeEmail(),
            'contrasena' => 'password', // el mutator encripta
            'rol' => 'Solicitante',
            'id_unidad' => function () {
                return \App\Models\Unidad::factory()->create()->id_unidad;
            },
            'telefono' => $this->faker->phoneNumber(),
            'activo' => 1
        ];
    }
}
