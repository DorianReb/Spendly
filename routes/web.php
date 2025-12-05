<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\GraficoController; // ← IMPORTANTE

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

/* HOME */
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');

/* RUTAS PROTEGIDAS */
Route::middleware('auth')->group(function () {

    /* === CATEGORÍAS === */
    Route::resource('categorias', CategoriaController::class)->except(['show']);

    Route::post('categorias/reorder', [CategoriaController::class, 'reorder'])
        ->name('categorias.reorder');

    Route::get('categoria', function () {
        return redirect()->route('categorias.index');
    })->name('categoria.legacy');


    /* === TRANSACCIONES === */
    Route::resource('transacciones', TransaccionController::class)
        ->parameters([
            'transacciones' => 'transaccion',
        ]);

    Route::get(
        'transacciones/categoria/{categoria}',
        [TransaccionController::class, 'porCategoria']
    )->name('transacciones.porCategoria');


    /* === METAS === */
    Route::resource('metas', MetaController::class);

    Route::post('metas/{meta}/aportes', [MetaController::class, 'storeAporte'])
        ->name('metas.aportes.store');


    /* === GRÁFICOS === */
    Route::get('/grafico', [GraficoController::class, 'index'])
        ->name('grafico.index');

    Route::view('/consejos', 'consejos.index')->name('consejos.consejos');

});
