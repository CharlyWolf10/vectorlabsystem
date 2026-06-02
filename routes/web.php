<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComprasExportController;
use App\Http\Controllers\PdfController;
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
    Route::get('/compras/export', [PdfController::class, 'exportCompras'])->name('compras.export');
    Route::get('/inventario', \App\Livewire\Inventario::class)->name('inventario');
    Route::get('/inventario/export', [PdfController::class, 'exportInventario'])->name('inventario.export');
    Route::get('/clientes', \App\Livewire\Clientes::class)->name('clientes');
    Route::get('/punto-de-venta', \App\Livewire\PuntoDeVenta::class)->name('pos');
    Route::get('/arqueos', \App\Livewire\Arqueos::class)->name('arqueos');
    Route::get('/usuarios', \App\Livewire\Usuarios::class)->name('usuarios');
});

require __DIR__.'/auth.php';
