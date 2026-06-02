<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\Attributes\On;

class Clientes extends Component
{
    public $search = '';
    public $selectedClientes = [];

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        $clientes = Cliente::where('nombre', 'like', '%' . $this->search . '%')
                           ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                           ->orWhere('email', 'like', '%' . $this->search . '%')
                           ->orWhere('matricula', 'like', '%' . $this->search . '%')
                           ->get();

        return view('livewire.clientes', compact('clientes'))->layout('layouts.app');
    }

    #[On('guardarCliente')]
    public function guardarCliente($data)
    {
        $id = $data['id'] ?? null;
        Cliente::updateOrCreate(
            ['id' => $id],
            [
                'nombre' => $data['nombre'],
                'apellidos' => $data['apellidos'],
                'es_estudiante' => $data['es_estudiante'],
                'matricula' => $data['es_estudiante'] ? $data['matricula'] : null,
                'escuela' => $data['es_estudiante'] ? ($data['escuela'] ?? null) : null,
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'limite_credito' => $data['limite_credito'] ?? 0,
                // Only set saldo_pendiente to 0 if it's a new record
                'saldo_pendiente' => $id ? Cliente::find($id)->saldo_pendiente : 0
            ]
        );
        
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Cliente guardado correctamente.']);
    }

    #[On('eliminarCliente')]
    public function eliminarCliente($id)
    {
        $cliente = Cliente::find($id);
        if ($cliente) {
            $cliente->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Cliente eliminado de la base de datos.']);
        }
    }
}
