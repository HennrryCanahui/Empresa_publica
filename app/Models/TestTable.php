<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TestTable extends Model
{
    protected $table = 'test_table'; // nombre exacto de la tabla
    protected $fillable = ['nombre']; // columnas que se pueden asignar
}

