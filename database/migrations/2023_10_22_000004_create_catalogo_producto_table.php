<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalogo_producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['MATERIAL', 'EQUIPO', 'HERRAMIENTA', 'SERVICIO', 'OTRO']);
            $table->foreignId('id_categoria')->constrained('categoria_producto', 'id_categoria');
            $table->string('unidad_medida');
            $table->decimal('precio_referencia', 10, 2);
            $table->text('especificaciones_tecnicas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogo_producto');
    }
};