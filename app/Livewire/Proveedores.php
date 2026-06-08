<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Proveedor;

class Proveedores extends Component
{
    public $proveedores;
    public $searchProveedores = '';
    public $selectedProveedores = [];
    public $selectAll = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->loadData();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProveedores = Proveedor::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedProveedores = [];
        }
    }

    #[On('clearSelection')]
    public function clearSelection()
    {
        $this->selectedProveedores = [];
        $this->selectAll = false;
    }

    public function loadData()
    {
        $this->proveedores = Proveedor::all();
    }

    public function render()
    {
        $proveedoresList = Proveedor::where('nombre', 'like', '%' . $this->searchProveedores . '%')
                                ->orWhere('email', 'like', '%' . $this->searchProveedores . '%')
                                ->get();

        return view('livewire.proveedores', ['proveedores' => $proveedoresList])->layout('layouts.app');
    }

    #[On('guardarProveedor')]
    public function guardarProveedor($data)
    {
        $id = $data['id'] ?? null;
        Proveedor::updateOrCreate(
            ['id' => $id],
            [
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'direccion' => $data['direccion'] ?? null,
                'rfc' => $data['rfc'] ?? null,
                'banco' => $data['banco'],
                'clabe' => $data['clabe'],
                'num_cuenta' => $data['num_cuenta'],
                'titular_cuenta' => $data['titular_cuenta'] ?? null,
            ]
        );
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Proveedor guardado correctamente.']);
    }

    #[On('eliminarProveedor')]
    public function eliminarProveedor($id)
    {
        $proveedor = Proveedor::find($id);
        if ($proveedor) {
            $proveedor->delete();
            $this->loadData();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Proveedor eliminado de la base de datos.']);
        }
    }
}
