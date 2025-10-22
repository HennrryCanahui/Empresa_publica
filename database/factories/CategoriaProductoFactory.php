<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaProductoFactory extends Factory
{
    protected $model = \App\Models\CategoriaProducto::class;

    public function definition()
    {
        return [
            'codigo' => $this->faker->unique()->bothify('C-###'),
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'activo' => 1
        ];
    }
}
