<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\Attributes\On;

class Clientes extends Component
{
    public $clientes;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->loadData();
    }

    public function loadData()
    {
        $this->clientes = Cliente::all();
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
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Cliente guardado correctamente.']);
    }

    public function render()
    {
        return view('livewire.clientes')->layout('layouts.app');
    }
}
