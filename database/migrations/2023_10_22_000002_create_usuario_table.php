<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('contrasena');
            $table->enum('rol', ['Administrador', 'Solicitante', 'Presupuesto', 'Compras', 'Autoridad']);
            $table->foreignId('id_unidad')->constrained('unidad', 'id_unidad');
            $table->string('telefono')->nullable();
            $table->timestamp('ultimo_acceso')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario');
    }
};