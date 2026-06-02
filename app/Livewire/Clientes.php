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
        Cliente::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'es_estudiante' => $data['es_estudiante'],
            'matricula' => $data['matricula'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
            'limite_credito' => $data['limite_credito'] ?? 0,
            'saldo_pendiente' => 0
        ]);
        
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Cliente guardado correctamente.']);
    }
}
