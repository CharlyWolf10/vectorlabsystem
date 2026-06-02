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
    public $filterProveedor = '';
    public $filterFaltantes = false;

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

    #[On('clearSelection')]
    public function clearSelection()
    {
        $this->selectedProductos = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $query = Producto::with('proveedor')
                         ->where(function($q) {
                             $q->where('nombre', 'like', '%' . $this->search . '%')
                               ->orWhere('codigo', 'like', '%' . $this->search . '%');
                         });
                         
        if ($this->filterProveedor) {
            $query->where('proveedor_id', $this->filterProveedor);
        }

        if ($this->filterFaltantes) {
            $query->whereColumn('stock', '<=', 'stock_minimo');
        }

        $productos = $query->get();
                             
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

    #[On('exportSelected')]
    public function exportSelected()
    {
        if (empty($this->selectedProductos)) {
            $this->dispatch('swal:error', ['title' => 'Atención', 'text' => 'Debes seleccionar al menos un producto para exportar.']);
            return;
        }

        $ids = implode(',', $this->selectedProductos);
        return redirect()->route('inventario.export', ['ids' => $ids]);
    }
}
