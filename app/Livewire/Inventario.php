<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\On;

class Inventario extends Component
{
    public $productos;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->loadData();
    }

    public function loadData()
    {
        $this->productos = Producto::all();
    }

    #[On('guardarProducto')]
    public function guardarProducto($data)
    {
        Producto::updateOrCreate(
            ['codigo' => $data['codigo']],
            [
                'nombre' => $data['nombre'],
                'precio_compra' => $data['precio_compra'],
                'precio_venta' => $data['precio_venta'],
                'stock' => $data['stock'],
                'stock_minimo' => $data['stock_minimo'],
            ]
        );
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Producto guardado correctamente.']);
    }

    public function render()
    {
        return view('livewire.inventario')->layout('layouts.app');
    }
}
