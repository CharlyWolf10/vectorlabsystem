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
        // Convertir a maysculas y limpiar espacios
        $this->nombre = strtoupper(trim($this->nombre ?? ''));
        $this->direccion = strtoupper(trim($this->direccion ?? ''));
        $this->rfc = strtoupper(trim($this->rfc ?? ''));
        $this->telefono = trim($this->telefono ?? '');

        $this->validate([
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|min:12|max:13',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255',
            'sitio_web' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048', // max 2MB
        ], [
            'telefono.digits' => 'El telfono debe tener exactamente 10 dgitos.',
            'rfc.min' => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max' => 'El RFC no puede exceder 13 caracteres.'
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
