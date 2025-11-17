<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TransaccionController; // ← FALTA ESTO

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// puedes dejar /home fuera del middleware, Laravel ya exige auth dentro del controlador
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');

Route::middleware('auth')->group(function () {

    // CATEGORÍAS
    Route::resource('categorias', CategoriaController::class)->except(['show']);

    Route::post('categorias/reorder', [CategoriaController::class, 'reorder'])
        ->name('categorias.reorder');

    Route::get('categoria', function () {
        return redirect()->route('categorias.index');
    })->name('categoria.legacy');

    // TRANSACCIONES
    Route::resource('transacciones', TransaccionController::class)
        ->parameters([
            'transacciones' => 'transaccion',
        ]);

    // Lista por categoría (segunda pantalla)
    Route::get(
        'transacciones/categoria/{categoria}',
        [TransaccionController::class, 'porCategoria']
    )->name('transacciones.porCategoria');
});
