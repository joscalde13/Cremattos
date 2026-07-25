<?php

use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');



Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::resource('productos', ProductoController::class)->except('show');
    Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('compras/{compra}/agregar-cantidad', [CompraController::class, 'agregarCantidad'])->name('compras.agregar-cantidad');
    Route::resource('ventas', VentaController::class)->except('index');
    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::post('packs/{pack}/sell', [VentaController::class, 'sellPack'])->name('packs.sell');
    Route::resource('packs', PackController::class)->except('show');



    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
 

});

require __DIR__.'/auth.php';
 