<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;

class Usuarios extends Component
{
    public $usuarios;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->loadData();
    }

    public function loadData()
    {
        $this->usuarios = User::all();
    }

    #[On('guardarUsuario')]
    public function guardarUsuario($data)
    {
        User::create([
            'name' => $data['nombre'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
        
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Usuario Creado!', 'text' => 'El empleado ha sido registrado correctamente.']);
    }

    #[On('eliminarUsuario')]
    public function eliminarUsuario($id)
    {
        if (auth()->id() == $id) {
            $this->dispatch('swal:error', ['title' => 'Error', 'text' => 'No puedes eliminar tu propio usuario.']);
            return;
        }

        User::find($id)->delete();
        $this->loadData();
        $this->dispatch('swal:success', ['title' => 'Usuario Eliminado', 'text' => 'El usuario ha sido eliminado correctamente.']);
    }

    public function render()
    {
        return view('livewire.usuarios')->layout('layouts.app');
    }
}
