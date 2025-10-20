<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestTableController;
Route::get('/', function () {
    return view('welcome');
});

Route::prefix('test')->group(function () {
    Route::get('/', [TestTableController::class, 'index'])->name('test.index');
    Route::get('/create', [TestTableController::class, 'create'])->name('test.create');
    Route::post('/', [TestTableController::class, 'store'])->name('test.store');
    Route::get('/{id}/edit', [TestTableController::class, 'edit'])->name('test.edit');
    Route::put('/{id}', [TestTableController::class, 'update'])->name('test.update');
    Route::delete('/{id}', [TestTableController::class, 'destroy'])->name('test.destroy');
});