<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestTableController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-table', [TestTableController::class, 'index']);