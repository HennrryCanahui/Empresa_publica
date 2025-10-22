<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogoProductoFactory extends Factory
{
    protected $model = \App\Models\CatalogoProducto::class;

    public function definition()
    {
        return [
            'codigo' => $this->faker->unique()->bothify('P-#####'),
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'tipo' => 'MATERIAL',
            'id_categoria' => function () {
                return \App\Models\CategoriaProducto::factory()->create()->id_categoria ?? 1;
            },
            'unidad_medida' => 'unidad',
            'precio_referencia' => $this->faker->randomFloat(2, 1, 1000),
            'activo' => 1
        ];
    }
}
