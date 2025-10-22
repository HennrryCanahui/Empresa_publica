<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadFactory extends Factory
{
    protected $model = \App\Models\Unidad::class;

    public function definition()
    {
        return [
            'codigo' => $this->faker->unique()->bothify('U-####'),
            'nombre' => $this->faker->company(),
            'tipo' => 'Operativa',
            'descripcion' => $this->faker->sentence(),
            'activo' => 1
        ];
    }
}
