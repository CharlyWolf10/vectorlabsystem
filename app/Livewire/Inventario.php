<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\On;

class Inventario extends Component
{
    public $search = '';
    public $selectedProductos = [];

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        $productos = Producto::where('nombre', 'like', '%' . $this->search . '%')
                             ->orWhere('codigo', 'like', '%' . $this->search . '%')
                             ->get();
                             
        return view('livewire.inventario', compact('productos'))->layout('layouts.app');
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
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Producto guardado correctamente.']);
    }

    // Render handled above
}
