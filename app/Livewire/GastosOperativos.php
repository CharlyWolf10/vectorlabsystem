<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GastoOperativo;
use Livewire\Attributes\On;

class GastosOperativos extends Component
{
    public $renta = 0;
    public $luz = 0;
    public $agua = 0;
    public $sueldos = 0;
    public $otros_gastos = 0;
    public $dias_por_mes = 24;
    public $horas_por_dia = 8;
    public $costo_por_minuto = 0;
    
    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        
        $this->loadData();
    }

    public function loadData()
    {
        $gasto = GastoOperativo::first();
        if ($gasto) {
            $this->renta = $gasto->renta;
            $this->luz = $gasto->luz;
            $this->agua = $gasto->agua;
            $this->sueldos = $gasto->sueldos;
            $this->otros_gastos = $gasto->otros_gastos;
            $this->dias_por_mes = $gasto->dias_por_mes;
            $this->horas_por_dia = $gasto->horas_por_dia;
            $this->costo_por_minuto = $gasto->costo_por_minuto;
        } else {
            $this->renta = 0;
            $this->luz = 0;
            $this->agua = 0;
            $this->sueldos = 0;
            $this->otros_gastos = 0;
            $this->dias_por_mes = 24;
            $this->horas_por_dia = 8;
            $this->costo_por_minuto = 0;
        }
    }

    public function guardar()
    {
        // Validar numéricos
        $this->validate([
            'renta' => 'numeric|min:0',
            'luz' => 'numeric|min:0',
            'agua' => 'numeric|min:0',
            'sueldos' => 'numeric|min:0',
            'otros_gastos' => 'numeric|min:0',
            'dias_por_mes' => 'numeric|min:1|max:31',
            'horas_por_dia' => 'numeric|min:1|max:24',
        ]);

        $gasto = GastoOperativo::first() ?? new GastoOperativo();

        $gasto->renta = $this->renta ?: 0;
        $gasto->luz = $this->luz ?: 0;
        $gasto->agua = $this->agua ?: 0;
        $gasto->sueldos = $this->sueldos ?: 0;
        $gasto->otros_gastos = $this->otros_gastos ?: 0;
        $gasto->dias_por_mes = $this->dias_por_mes ?: 24;
        $gasto->horas_por_dia = $this->horas_por_dia ?: 8;

        // Calcular costo por minuto
        $total_gastos = $gasto->renta + $gasto->luz + $gasto->agua + $gasto->sueldos + $gasto->otros_gastos;
        $total_minutos = $gasto->dias_por_mes * $gasto->horas_por_dia * 60;

        if ($total_minutos > 0) {
            $gasto->costo_por_minuto = $total_gastos / $total_minutos;
        } else {
            $gasto->costo_por_minuto = 0;
        }

        $gasto->save();
        $this->costo_por_minuto = $gasto->costo_por_minuto;

        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Gastos operativos actualizados correctamente.']);
    }

    public function render()
    {
        return view('livewire.gastos-operativos')->layout('layouts.app');
    }
}
