<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComprasExportController;
use App\Livewire\ComprasYPagos;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/compras-pagos', ComprasYPagos::class)->name('compras');
    Route::get('/compras/export', [ComprasExportController::class, 'exportPdf'])->name('compras.export');
    Route::get('/inventario', \App\Livewire\Inventario::class)->name('inventario');
    Route::get('/clientes', \App\Livewire\Clientes::class)->name('clientes');
    Route::get('/punto-de-venta', \App\Livewire\PuntoDeVenta::class)->name('pos');
});

require __DIR__.'/auth.php';
