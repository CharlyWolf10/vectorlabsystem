<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\On;

class Inventario extends Component
{
    public $search = '';
    public $selectedProductos = [];
    public $selectAll = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProductos = Producto::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedProductos = [];
        }
    }

    public function render()
    {
        $productos = Producto::with('proveedor')
                             ->where('nombre', 'like', '%' . $this->search . '%')
                             ->orWhere('codigo', 'like', '%' . $this->search . '%')
                             ->get();
                             
        $proveedores = \App\Models\Proveedor::all();
                             
        return view('livewire.inventario', compact('productos', 'proveedores'))->layout('layouts.app');
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
                'proveedor_id' => $data['proveedor_id'] ?? null,
            ]
        );
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Producto guardado correctamente.']);
    }

    #[On('eliminarProducto')]
    public function eliminarProducto($id)
    {
        $producto = Producto::find($id);
        if ($producto) {
            $producto->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Producto eliminado del inventario.']);
        }
    }

    // Render handled above
}
