<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BusinessProfile as BusinessProfileModel;

class BusinessProfile extends Component
{
    use WithFileUploads;

    public $nombre;
    public $direccion;
    public $rfc;
    public $telefono;
    public $correo;
    public $sitio_web;
    public $logo; // For new upload
    public $current_logo_path;

    public function mount()
    {
        $profile = BusinessProfileModel::first();
        if ($profile) {
            $this->nombre = $profile->nombre;
            $this->direccion = $profile->direccion;
            $this->rfc = $profile->rfc;
            $this->telefono = $profile->telefono;
            $this->correo = $profile->correo;
            $this->sitio_web = $profile->sitio_web;
            $this->current_logo_path = $profile->logo_path;
        }
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'sitio_web' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048', // max 2MB
        ]);

        $profile = BusinessProfileModel::first() ?? new BusinessProfileModel();

        if ($this->logo) {
            $path = $this->logo->store('logos', 'public');
            $profile->logo_path = 'storage/' . $path;
            $this->current_logo_path = $profile->logo_path;
        }

        $profile->nombre = $this->nombre;
        $profile->direccion = $this->direccion;
        $profile->rfc = $this->rfc;
        $profile->telefono = $this->telefono;
        $profile->correo = $this->correo;
        $profile->sitio_web = $this->sitio_web;
        $profile->save();

        session()->flash('message', 'Datos del negocio actualizados correctamente.');
        $this->dispatch('swal:success', ['title' => '¡Guardado!', 'text' => 'Datos actualizados correctamente.']);
    }

    public function render()
    {
        return view('livewire.settings.business-profile')->layout('layouts.app');
    }
}
