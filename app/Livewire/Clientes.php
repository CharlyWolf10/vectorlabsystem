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
            'telefono' => $data['telefono'],
            'email' => $data['email'],
            'limite_credito' => $data['limite_credito'],
            'saldo_actual' => 0
        ]);
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Cliente guardado correctamente.']);
    }

    public function render()
    {
        return view('livewire.clientes')->layout('layouts.app');
    }
}
