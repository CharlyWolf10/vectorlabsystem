<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;

class Usuarios extends Component
{
    public $search = '';
    public $selectedUsuarios = [];
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsuarios = User::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUsuarios = [];
        }
    }

    #[On('clearSelection')]
    public function clearSelection()
    {
        $this->selectedUsuarios = [];
        $this->selectAll = false;
    }

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
                     ->orWhere('email', 'like', '%' . $this->search . '%')
                     ->orWhere('role', 'like', '%' . $this->search . '%')
                     ->get();

        return view('livewire.usuarios', compact('users'))->layout('layouts.app');
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
        $this->dispatch('swal:success', ['title' => 'Usuario Eliminado', 'text' => 'El usuario ha sido eliminado correctamente.']);
    }
}
